<?php

namespace App\Console\Commands;

use App\Models\Package;
use App\Models\PackageDetail;
use App\Models\PackageFaq;
use App\Models\PackageItinerary;
use App\Models\PackageMetaData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Pilot importer for the old indianpanorama.in tour package pages.
 *
 * Old pages carry no primary_image and no category — those are left null on
 * purpose (per scoping decision): primary_image because packages here must
 * reference an already-uploaded Media Library asset and this pass is
 * text-only, category because the old site's per-state theme groupings
 * (Beach & Backwaters, Culture, etc.) need a real taxonomy decision, not an
 * import-time guess. Imported packages are created as drafts (is_draft=true,
 * is_active=false) so nothing goes live before review.
 */
class ImportOldSitePackages extends Command
{
    protected $signature = 'packages:import-old-site {--state=kerala} {--dry-run}';
    protected $description = 'Scrape tour packages from the old indianpanorama.in site and import them as draft Packages';

    /** Per-state config: which pages to scrape + which existing Location/State rows to attach to. */
    private array $states = [
        'kerala' => [
            'state_id'          => 7,
            'location_id'       => 251, // "Kerala" umbrella location
            'source_location_id' => 73,  // Cochin — typical gateway city
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-backwaters.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-houseboat-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/beaches-of-kerala.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-beach-backwater-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/best-of-kerala-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/classic-kerala-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-cultural-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/exotic-kerala-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/ayurveda-tour-packages.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-temple-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-homestay-experience.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-honeymoon-packages.php',
                'https://www.indianpanorama.in/india-tour-itinerary/churches-in-kerala.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-trekking-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-adventure-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/exclusive-wayanad-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/kerala-weekend-tour.php',
            ],
        ],
        'rajasthan' => [
            'state_id'           => 1,
            'location_id'        => 1,  // Jaipur — Rajasthan's capital, no state-level umbrella location exists
            'source_location_id' => 66, // Delhi — typical gateway city for Rajasthan tours
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/camel-safari-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/cities-of-rajasthan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/colourful-rajasthan-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/destinations-of-rajasthan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/four-corners-of-rajasthan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/golden-triangle-trip.php',
                'https://www.indianpanorama.in/india-tour-itinerary/golden-triangle-wildlife-and-rural-rajasthan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highlights-of-rajasthan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/palace-on-wheels-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/palaces-of-rajasthan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/rajasthan-wildlife-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/rajasthan-with-tajmahal-ganges.php',
                'https://www.indianpanorama.in/india-tour-itinerary/rural-rajasthan-tour.php',
            ],
        ],
        'tamilnadu' => [
            'state_id'           => 6,
            'location_id'        => 39, // Chennai — capital & main gateway for Tamil Nadu
            'source_location_id' => 39,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/arupadai-veedu.php',
                'https://www.indianpanorama.in/india-tour-itinerary/astrological-tourism.php',
                'https://www.indianpanorama.in/india-tour-itinerary/beaches-of-tamilnadu.php',
                'https://www.indianpanorama.in/india-tour-itinerary/culture-of-tamil-nadu.php',
                'https://www.indianpanorama.in/india-tour-itinerary/jain-circuit-in-madurai.php',
                'https://www.indianpanorama.in/india-tour-itinerary/naadi-astrology.php',
                'https://www.indianpanorama.in/india-tour-itinerary/temples-of-tamilnadu.php',
            ],
        ],
        'karnataka' => [
            'state_id'           => 8,
            'location_id'        => 54, // Bangalore — capital & main gateway for Karnataka
            'source_location_id' => 54,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/beaches-of-karnataka.php',
                'https://www.indianpanorama.in/india-tour-itinerary/best-of-karnataka.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highilights-of-karnataka.php',
                'https://www.indianpanorama.in/india-tour-itinerary/karnataka-adventure-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/karnataka-heritage-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/karnataka-hills-wildlife-tour.php',
            ],
        ],
        'north-india' => [
            'location_id'        => 66, // Delhi — regional gateway hub (multi-state region, no single state)
            'source_location_id' => 66,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/best-of-north-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/follow-the-footsteps-of-lord-buddha.php',
                'https://www.indianpanorama.in/india-tour-itinerary/golden-triangle-with-haridwar-rishikesh-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/grand-himachal-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/grand-tour-of-himachal-pradesh-ladakh-and-kashmir.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highlights-of-himachal-pradesh-uttrakhand.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highlights-of-himachal-pradesh.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highlights-of-uttarakhand.php',
                'https://www.indianpanorama.in/india-tour-itinerary/holy-varanasi.php',
                'https://www.indianpanorama.in/india-tour-itinerary/indian-nepal-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/leh-ladakh.php',
                'https://www.indianpanorama.in/india-tour-itinerary/majestic-north-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/majestic-taj-mahal.php',
                'https://www.indianpanorama.in/india-tour-itinerary/north-india-with-kathmandu-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/north-indian-wildlife-spectacular.php',
                'https://www.indianpanorama.in/india-tour-itinerary/north-of-delhi-himachal-and-uttarakhand.php',
                'https://www.indianpanorama.in/india-tour-itinerary/spiritual-journey-to-the-himalayas.php',
                'https://www.indianpanorama.in/india-tour-itinerary/splendours-of-ladakh-and-kashmir.php',
                'https://www.indianpanorama.in/india-tour-itinerary/taj-mahal-and-a-taste-of-rajasthan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/tiger-trail-in-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/yoga-and-meditation-in-india.php',
            ],
        ],
        'south-india' => [
            'location_id'        => 39, // Chennai — regional gateway hub (multi-state region: Kerala/TN/Karnataka/Goa)
            'source_location_id' => 39,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/art-and-treasures-of-karnataka-goa-and-mumbai.php',
                'https://www.indianpanorama.in/india-tour-itinerary/art-and-treasures-of-tamilnadu-and-kerala.php',
                'https://www.indianpanorama.in/india-tour-itinerary/beaches-of-goa.php',
                'https://www.indianpanorama.in/india-tour-itinerary/complete-south-india-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/culinary-tour-of-south-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/elephants-of-south-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/exotic-tamilnadu-kerala-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/goa-and-kerala-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highlights-of-south-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/homestays-and-heritage-hotels-of-south-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/ooty-and-munnar-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/south-india-explorer.php',
                'https://www.indianpanorama.in/india-tour-itinerary/south-india-hill-station-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/south-indian-churches.php',
                'https://www.indianpanorama.in/india-tour-itinerary/southern-india-beaches-hills-and-wildlife.php',
                'https://www.indianpanorama.in/india-tour-itinerary/southern-india-coastal-explorer.php',
                'https://www.indianpanorama.in/india-tour-itinerary/southern-india-nature-and-wildlife.php',
                'https://www.indianpanorama.in/india-tour-itinerary/womens-tour-in-southindia.php',
            ],
        ],
        'west-india' => [
            'location_id'        => 37, // Mumbai — regional gateway hub (Gujarat & Maharashtra)
            'source_location_id' => 37,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/beaches-of-maharashtra.php',
                'https://www.indianpanorama.in/india-tour-itinerary/fascinating-faces-of-gujarat.php',
                'https://www.indianpanorama.in/india-tour-itinerary/glimpse-of-western-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/gujarat-tribal-and-cultural-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highlights-of-gujarat.php',
                'https://www.indianpanorama.in/india-tour-itinerary/history-and-architecture-of-gujarat.php',
                'https://www.indianpanorama.in/india-tour-itinerary/shirdi-tour-packages.php',
                'https://www.indianpanorama.in/india-tour-itinerary/spiritual-tour-package.php',
                'https://www.indianpanorama.in/india-tour-itinerary/wildlife-of-gujarat.php',
            ],
        ],
        'central-india' => [
            'location_id'        => 264, // Khajuraho — signature destination for this region
            'source_location_id' => 264,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/central-india-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/historic-and-spiritual-journey-to-central-north-india.php',
            ],
        ],
        'east-india' => [
            'location_id'        => 87, // Kolkata — regional gateway hub (West Bengal, Odisha, Sikkim, Assam, NE India)
            'source_location_id' => 87,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/beaches-of-odisha.php',
                'https://www.indianpanorama.in/india-tour-itinerary/calcutta-sundarbans-and-sikkim-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/eastern-himalayan.php',
                'https://www.indianpanorama.in/india-tour-itinerary/highlights-of-west-bengal-and-sikkim.php',
                'https://www.indianpanorama.in/india-tour-itinerary/hornbill-and-tea-festival.php',
                'https://www.indianpanorama.in/india-tour-itinerary/journey-to-eastern-assam.php',
                'https://www.indianpanorama.in/india-tour-itinerary/odisha-marvels.php',
                'https://www.indianpanorama.in/india-tour-itinerary/orissa-up-close.php',
                'https://www.indianpanorama.in/india-tour-itinerary/splendid-sojourn-in-north-east-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/tea-tribes-and-rhinos-north-east-india-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/west-bengal-sikkim-and-assam.php',
            ],
        ],
        'complete-india' => [
            'location_id'        => 66, // Delhi — pan-India gateway hub
            'source_location_id' => 66,
            'urls' => [
                'https://www.indianpanorama.in/india-tour-itinerary/best-of-north-and-south-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/buddhist-circuit-in-andhra-pradesh.php',
                'https://www.indianpanorama.in/india-tour-itinerary/complete-taste-of-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/golden-triangle-trip.php',
                'https://www.indianpanorama.in/india-tour-itinerary/indian-wildlife-tour.php',
                'https://www.indianpanorama.in/india-tour-itinerary/palaces-temples-and-wildlife-of-north-and-south-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/textile-tour-of-india.php',
                'https://www.indianpanorama.in/india-tour-itinerary/wildlife-of-north-and-south-india.php',
            ],
        ],
    ];

    public function handle(): int
    {
        $stateKey = $this->option('state');
        $dryRun   = $this->option('dry-run');

        if (!isset($this->states[$stateKey])) {
            $this->error("Unknown state '{$stateKey}'. Configured: " . implode(', ', array_keys($this->states)));
            return self::FAILURE;
        }

        $config = $this->states[$stateKey];
        $urls   = $config['urls'];

        $this->info(($dryRun ? '[DRY RUN] ' : '') . "Importing {$stateKey}: " . count($urls) . ' package pages');

        $ok = 0;
        $skipped = 0;
        $failed = [];

        foreach ($urls as $url) {
            if (Package::where('source_url', $url)->exists()) {
                $this->line("Already imported, skipping: {$url}");
                $skipped++;
                continue;
            }

            $this->line("Fetching {$url}");

            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])->timeout(30)->get($url);

                if (!$response->successful()) {
                    $this->warn("  -> HTTP {$response->status()}, skipping");
                    $failed[] = $url;
                    continue;
                }

                $data = $this->parsePage($response->body(), $url);

                if (!$data) {
                    $this->warn('  -> Could not parse required fields, skipping');
                    $failed[] = $url;
                    continue;
                }

                // Catches packages that already exist in the DB under the exact same
                // title but predate source_url tracking (e.g. manually created before
                // this importer existed) — the source_url check alone can't see those.
                $existingByTitle = Package::where('title', $data['title'])->whereNull('source_url')->first();
                if ($existingByTitle) {
                    $this->warn("  -> \"{$data['title']}\" already exists as package #{$existingByTitle->id} (no source_url — pre-existing), skipping");
                    $skipped++;
                    continue;
                }

                $this->line("  -> \"{$data['title']}\" — {$data['duration_days']}D/{$data['duration_nights']}N, " . count($data['itinerary']) . ' itinerary days, ' . count($data['faqs']) . ' FAQs');

                if (!$dryRun) {
                    $this->savePackage($data, $config);
                }

                $ok++;
            } catch (\Throwable $e) {
                $this->error("  -> Error: {$e->getMessage()}");
                $failed[] = $url;
            }

            usleep(400_000); // be polite to the old site
        }

        $this->info("Done. {$ok} succeeded, {$skipped} already-imported skipped, " . count($failed) . ' failed.');
        if ($failed) {
            $this->warn('Failed URLs: ' . implode(', ', $failed));
        }

        return self::SUCCESS;
    }

    private function parsePage(string $html, string $url): ?array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        $title = $this->text($xpath, '//h1');
        if (!$title) {
            return null;
        }

        $metaTitle       = $this->attr($xpath, '//title', null) ?: $this->text($xpath, '//title');
        $metaDescription = $this->metaContent($xpath, 'description');
        $metaKeywords    = $this->metaContent($xpath, 'keywords');
        $ogImage         = $this->metaProperty($xpath, 'og:image');

        // Duration lives in a <small> right after the <h1>, e.g. "5 Days / 4 Nights"
        $durationText = $this->text($xpath, '//h1/following-sibling::small[1]');
        $days = $nights = 0;
        if ($durationText && preg_match('/(\d+)\s*Days?\s*\/\s*(\d+)\s*Nights?/i', $durationText, $m)) {
            $days   = (int) $m[1];
            $nights = (int) $m[2];
        }

        // Intro paragraphs (short description) — direct <p> children of the intro block,
        // which DOMDocument naturally excludes if they're HTML-commented-out on the source page.
        $introNodes = $xpath->query('//div[contains(concat(" ", normalize-space(@class), " "), " col-xl-11 ") and contains(concat(" ", normalize-space(@class), " "), " pt-5 ")]/p');
        $shortDescription = '';
        if ($introNodes) {
            foreach ($introNodes as $p) {
                $shortDescription .= $this->outerHtml($p);
            }
        }

        // Itinerary: <ul class="itinerary-day"> > <li> > *.card-title (day title, h3 or h4 depending on page) + itinerary-detail (day body)
        $itinerary = [];
        $dayNodes = $xpath->query('//ul[contains(@class,"itinerary-day")]/li');
        if ($dayNodes) {
            foreach ($dayNodes as $li) {
                $dayTitle = $this->text($xpath, './/*[contains(@class,"card-title")]', $li);
                $detailNode = $xpath->query('.//div[contains(@class,"itinerary-detail")]', $li)->item(0);
                if (!$dayTitle || !$detailNode) {
                    continue;
                }
                $dayTitle = preg_replace('/\s+/', ' ', trim($dayTitle));
                $dayTitle = preg_replace('/\s*:\s*/', ': ', $dayTitle, 1);
                $itinerary[] = [
                    'title'   => $dayTitle,
                    'details' => $this->innerHtml($detailNode),
                ];
            }
        }

        // Some newer pages drop the "X Days / Y Nights" banner subtitle entirely —
        // fall back to deriving duration from the itinerary itself rather than
        // storing a misleading 0D/0N for a package that clearly has real content.
        if ($days === 0 && $nights === 0 && count($itinerary) > 0) {
            $days   = count($itinerary);
            $nights = max(0, $days - 1);
        }

        // FAQs: schema.org FAQPage accordion — button[itemprop=name] / div[itemprop=text]
        $faqs = [];
        $faqNodes = $xpath->query('//div[@class="accordion-item"]');
        if ($faqNodes) {
            foreach ($faqNodes as $item) {
                $question = $this->text($xpath, './/*[@itemprop="name"]', $item);
                $answerNode = $xpath->query('.//*[@itemprop="text"]', $item)->item(0);
                if (!$question || !$answerNode) {
                    continue;
                }
                $faqs[] = [
                    'question' => trim($question),
                    'answer'   => $this->innerHtml($answerNode),
                ];
            }
        }

        return [
            'source_url'        => $url,
            'title'             => trim($title),
            'meta_title'        => $metaTitle ? trim($metaTitle) : null,
            'meta_description'  => $metaDescription,
            'meta_keywords'     => $metaKeywords,
            'source_image_url'  => $ogImage,
            'duration_days'     => $days,
            'duration_nights'   => $nights,
            'short_description' => trim($shortDescription),
            'itinerary'         => $itinerary,
            'faqs'              => $faqs,
        ];
    }

    private function savePackage(array $data, array $config): void
    {
        DB::transaction(function () use ($data, $config) {
            $baseSlug = $this->buildSlug($data['title'], $data['duration_days'], $data['duration_nights']);
            $slug     = $this->uniqueSlug($baseSlug);

            $package = Package::create([
                'title'              => $data['title'],
                'slug'               => $slug,
                'source_url'         => $data['source_url'],
                'location_id'        => $config['location_id'],
                'source_location_id' => $config['source_location_id'],
                'country_id'         => 1,
                'short_description'  => $data['short_description'],
                'is_active'          => false,
                'is_draft'           => true,
            ]);

            PackageDetail::create([
                'package_id'      => $package->id,
                'duration_days'   => $data['duration_days'],
                'duration_nights' => $data['duration_nights'],
                // The admin edit form's "Short Description" field reads from here
                // (PackageDetail.tour_highlights), separately from Package.short_description
                // above (which is what the public API/website actually serve) — kept in sync.
                'tour_highlights' => $data['short_description'],
            ]);

            PackageMetaData::create([
                'package_id'        => $package->id,
                'meta_title'        => $data['meta_title'],
                'meta_description'  => $data['meta_description'],
                'meta_keywords'     => $data['meta_keywords'],
                'h1_heading'        => $data['title'],
            ]);

            foreach ($data['itinerary'] as $day) {
                PackageItinerary::create([
                    'package_id' => $package->id,
                    'title'      => $day['title'],
                    'details'    => $day['details'],
                ]);
            }

            foreach ($data['faqs'] as $faq) {
                PackageFaq::create([
                    'package_id' => $package->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'],
                ]);
            }
        });
    }

    private function buildSlug(string $title, $days, $nights): string
    {
        $slug   = Str::slug($title);
        $days   = (int) $days;
        $nights = (int) $nights;

        if ($days > 0) {
            $slug .= '-' . $days . '-' . ($days === 1 ? 'day' : 'days');
        }
        if ($nights > 0) {
            $slug .= '-' . $nights . '-' . ($nights === 1 ? 'night' : 'nights');
        }

        return $slug;
    }

    private function uniqueSlug(string $base): string
    {
        $slug    = $base;
        $counter = 1;
        while (Package::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter++;
        }
        return $slug;
    }

    // ── DOM helpers ──────────────────────────────────────────────────────────

    private function text(\DOMXPath $xpath, string $query, ?\DOMNode $context = null): ?string
    {
        $nodes = $context ? $xpath->query($query, $context) : $xpath->query($query);
        if (!$nodes || $nodes->length === 0) return null;
        return trim(preg_replace('/\s+/', ' ', $nodes->item(0)->textContent));
    }

    private function attr(\DOMXPath $xpath, string $query, ?string $default = null): ?string
    {
        $nodes = $xpath->query($query);
        if (!$nodes || $nodes->length === 0) return $default;
        return trim($nodes->item(0)->textContent) ?: $default;
    }

    private function metaContent(\DOMXPath $xpath, string $name): ?string
    {
        $nodes = $xpath->query("//meta[@name=\"{$name}\"]/@content");
        if (!$nodes || $nodes->length === 0) return null;
        return trim($nodes->item(0)->textContent);
    }

    private function metaProperty(\DOMXPath $xpath, string $property): ?string
    {
        $nodes = $xpath->query("//meta[@property=\"{$property}\"]/@content");
        if (!$nodes || $nodes->length === 0) return null;
        return trim($nodes->item(0)->textContent);
    }

    private function innerHtml(\DOMNode $node): string
    {
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $node->ownerDocument->saveHTML($child);
        }
        return trim($html);
    }

    private function outerHtml(\DOMNode $node): string
    {
        return $node->ownerDocument->saveHTML($node);
    }
}
