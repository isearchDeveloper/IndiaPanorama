// Shared CMS API response types
// Inferred from actual API response at /page/setting/* endpoints
//
// Rule: structural fields (id, type, sort_order) are required.
// All content fields are optional — CMS can omit any field at any time
// without breaking the frontend. Components must supply their own fallbacks.

export interface CmsHeroContent {
  heading?: string;
  subheading?: string;
  banner_image?: string;
  banner_image_alt?: string;
  overlay_opacity?: string;
  cta_label?: string;
  cta_url?: string;
}

export interface CmsImageTextContent {
  heading?: string;
  image_position?: "left" | "right";
  image?: string;
  image_alt?: string;
  body?: string;
}

export interface CmsCardItem {
  image?: string;
  image_alt?: string;
  title?: string;
  description?: string;
}

export interface CmsCardsContent {
  heading?: string;
  layout?: string;
  subheading?: string;
  bg_image?: string;
  bg_image_alt?: string;
  cta_label?: string;
  cta_url?: string;
  cards?: CmsCardItem[];
}

export interface CmsExperienceItem {
  icon?: string;
  title?: string;
}

export interface CmsExperienceContent {
  heading?: string;
  description?: string;
  bg_image?: string;
  bg_image_alt?: string;
  items?: CmsExperienceItem[];
}

export interface CmsAwardsContent {
  heading?: string;
  layout?: string;
  filter?: string;
  limit?: string;
  show_year?: string;
  award_ids?: number[];
}

export interface CmsAwardItem {
  id: number;
  title?: string;
  award_year?: string;
  description?: string;
  banner_image?: string;
}

export interface CmsCtaContent {
  heading?: string;
  background?: string;
  subheading?: string;
  button_label?: string;
  button_url?: string;
  image?: string;
  image_alt?: string;
}

export interface CmsTextContent {
  heading?: string;
  body?: string;
}

export interface CmsTeamMember {
  id: number;
  name?: string;
  description?: string;
  about?: string;
  profile_image?: string;
  department?: { id: number; name: string } | null;
}

export interface CmsDepartment {
  id?: number | null;
  name?: string;
}

// ── Section discriminated union ───────────────────────────────────────────
// type + id + sort_order are always present — everything else may be missing

export type CmsSectionType =
  | "hero"
  | "image_text"
  | "cards"
  | "experience"
  | "awards"
  | "cta"
  | "text"
  | "team";

interface CmsSectionBase {
  id: number;
  label?: string;
  sort_order: number;
}

export interface CmsHeroSection extends CmsSectionBase {
  type: "hero";
  content: CmsHeroContent;
}

export interface CmsImageTextSection extends CmsSectionBase {
  type: "image_text";
  content: CmsImageTextContent;
}

export interface CmsCardsSection extends CmsSectionBase {
  type: "cards";
  content: CmsCardsContent;
}

export interface CmsExperienceSection extends CmsSectionBase {
  type: "experience";
  content: CmsExperienceContent;
}

export interface CmsAwardsSection extends CmsSectionBase {
  type: "awards";
  content: CmsAwardsContent;
  awards?: CmsAwardItem[];
}

export interface CmsCtaSection extends CmsSectionBase {
  type: "cta";
  content: CmsCtaContent;
}

export interface CmsTextSection extends CmsSectionBase {
  type: "text";
  content: CmsTextContent;
}

export interface CmsTeamSection extends CmsSectionBase {
  type: "team";
  members?: CmsTeamMember[];
  departments?: CmsDepartment[];
}

export type CmsSection =
  | CmsHeroSection
  | CmsImageTextSection
  | CmsCardsSection
  | CmsExperienceSection
  | CmsAwardsSection
  | CmsCtaSection
  | CmsTextSection
  | CmsTeamSection;

export interface CmsPageData {
  title?: string;
  slug?: string;
  seo?: {
    meta_title?: string | null;
    meta_description?: string | null;
    meta_keywords?: string | null;
    h1_heading?: string | null;
    meta_details?: string | null;
  };
  sections?: CmsSection[];
}
