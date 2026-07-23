import type { Metadata } from "next";
import Banner from "@/app/components/common/Banner";
import Breadcrumb from "@/app/components/common/Breadcrumb";
import styles from "./page.module.css";

export const metadata: Metadata = {
  title: "Cancellation & Refund Policy | Indian Panorama",
  description:
    "Understand Indian Panorama's cancellation and refund policy for tour packages, hotels, and flights. Clear timelines, fair refund processing, and transparent charges.",
  openGraph: {
    title: "Cancellation & Refund Policy | Indian Panorama",
    description:
      "Clear cancellation timelines and refund information for Indian Panorama tour bookings.",
    type: "website",
    url: "https://www.indianpanorama.in/cancellation-refund-policy",
  },
  twitter: {
    card: "summary",
    title: "Cancellation & Refund Policy | Indian Panorama",
    description: "Cancellation timelines and refund processing details for Indian Panorama tours.",
  },
  alternates: { canonical: "https://www.indianpanorama.in/cancellation-refund-policy" },
  robots: { index: true, follow: true },
};

export default function CancellationPolicyPage() {
  return (
    <div className={styles.page}>
      <Banner
        title="Cancellation & Refund Policy"
        subtitle="Transparent, fair, and clearly defined — because your peace of mind matters to us"
        bgImage="/images/about-banner-pages.jpg"
      />

      <div className={styles.container}>
        <div className={styles.breadcrumbRow}>
          <Breadcrumb
            items={[
              { label: "Home", href: "/" },
              { label: "Cancellation & Refund Policy", href: "/cancellation-refund-policy" },
            ]}
          />
        </div>

        <div className={styles.pageHeader}>
          <span className={styles.badge}>Last Updated: January 1, 2025</span>
          <h1 className={styles.pageTitle}>Cancellation &amp; Refund Policy</h1>
          <p className={styles.pageIntro}>
            We understand that travel plans can change unexpectedly. Indian Panorama&apos;s cancellation
            policy is designed to be as fair and transparent as possible while accounting for costs
            incurred with hotels, airlines, and service partners on your behalf. Please read this
            policy carefully before confirming your booking.
          </p>
        </div>

        <div className={styles.mainContent}>

          {/* ── SECTION 1: Cancellation Timeline ── */}
          <section className={styles.section}>
            <div className={styles.sectionHeader}>
              <span className={styles.sectionNumber}>1</span>
              <h2 className={styles.sectionTitle}>Tour Package Cancellation Timeline</h2>
            </div>
            <p className={styles.sectionText}>
              Cancellation charges are calculated based on the number of days before your scheduled
              departure date that we receive your written cancellation request. All charges are
              percentages of the <strong>total tour cost</strong> including accommodation, transfers,
              and included activities.
            </p>

            {/* Timeline Cards */}
            <div className={styles.timelineGrid}>
              <div className={`${styles.timelineCard} ${styles.green}`}>
                <span className={styles.timelineIcon}>✅</span>
                <div>
                  <p className={styles.timelineDays}>30+ Days</p>
                  <p className={styles.timelineDaysLabel}>before departure</p>
                </div>
                <span className={styles.timelineDeduction}>25% Deduction</span>
                <p className={styles.timelineNote}>
                  Only the booking deposit (25%) is forfeited. The remaining 75% of tour cost is
                  fully refunded within 7–10 business days.
                </p>
              </div>

              <div className={`${styles.timelineCard} ${styles.amber}`}>
                <span className={styles.timelineIcon}>⏳</span>
                <div>
                  <p className={styles.timelineDays}>15–29 Days</p>
                  <p className={styles.timelineDaysLabel}>before departure</p>
                </div>
                <span className={styles.timelineDeduction}>50% Deduction</span>
                <p className={styles.timelineNote}>
                  50% of the total tour cost is charged as cancellation fees. The remaining 50%
                  is refunded within 10–14 business days.
                </p>
              </div>

              <div className={`${styles.timelineCard} ${styles.orange}`}>
                <span className={styles.timelineIcon}>⚠️</span>
                <div>
                  <p className={styles.timelineDays}>7–14 Days</p>
                  <p className={styles.timelineDaysLabel}>before departure</p>
                </div>
                <span className={styles.timelineDeduction}>75% Deduction</span>
                <p className={styles.timelineNote}>
                  75% of the total tour cost is forfeited. Only 25% may be refunded, subject to
                  recovery from hotels and service providers.
                </p>
              </div>

              <div className={`${styles.timelineCard} ${styles.red}`}>
                <span className={styles.timelineIcon}>🚫</span>
                <div>
                  <p className={styles.timelineDays}>Less than 7 Days</p>
                  <p className={styles.timelineDaysLabel}>before departure</p>
                </div>
                <span className={styles.timelineDeduction}>No Refund</span>
                <p className={styles.timelineNote}>
                  No refund is available for cancellations within 7 days of departure. We strongly
                  recommend comprehensive travel insurance for this situation.
                </p>
              </div>
            </div>

            <div className={styles.noteBox}>
              <p>
                <strong>Important:</strong> The &ldquo;day of departure&rdquo; is Day Zero. For example, if you
                depart on 1st March and cancel on 15th February, that is 14 days before departure and
                the 7–14 day policy applies (75% deduction).
              </p>
            </div>
          </section>

          {/* ── SECTION 2: Specific Policies ── */}
          <section className={styles.section}>
            <div className={styles.sectionHeader}>
              <span className={styles.sectionNumber}>2</span>
              <h2 className={styles.sectionTitle}>Service-Specific Cancellation Policies</h2>
            </div>
            <p className={styles.sectionText}>
              Different travel components have their own cancellation rules governed by suppliers.
              The following applies in addition to our standard timeline:
            </p>

            <div className={styles.policyGrid}>
              <div className={styles.policyCard}>
                <div className={styles.policyCardHeader}>
                  <div className={styles.policyCardIcon}>✈️</div>
                  <h3 className={styles.policyCardTitle}>Flight Cancellations</h3>
                </div>
                <p className={styles.policyCardText}>
                  Flight tickets are subject to the airline&apos;s own cancellation and refund policy. Indian
                  Panorama passes through refunds received from airlines minus processing fees
                  (₹1,500–₹3,000 per ticket). Low-cost carrier tickets are typically non-refundable.
                  We recommend purchasing refundable fare classes for flexibility.
                </p>
              </div>

              <div className={styles.policyCard}>
                <div className={styles.policyCardHeader}>
                  <div className={styles.policyCardIcon}>🏨</div>
                  <h3 className={styles.policyCardTitle}>Hotel Cancellations</h3>
                </div>
                <p className={styles.policyCardText}>
                  Hotels during peak season (October–March) and major festivals may have stricter
                  no-cancellation or no-refund policies. Heritage palaces, wildlife lodges, and
                  houseboat bookings in Kerala often require full payment upfront and are
                  non-refundable within 30 days of arrival. These will be clearly communicated
                  at booking.
                </p>
              </div>

              <div className={styles.policyCard}>
                <div className={styles.policyCardHeader}>
                  <div className={styles.policyCardIcon}>🚗</div>
                  <h3 className={styles.policyCardTitle}>Transportation</h3>
                </div>
                <p className={styles.policyCardText}>
                  Private car, train, and coach transfers that are cancelled more than 48 hours
                  before the service are eligible for a full refund of the transport component.
                  Cancellations within 48 hours will incur a 100% charge on the transportation
                  cost. Train tickets are subject to Indian Railways refund rules.
                </p>
              </div>

              <div className={styles.policyCard}>
                <div className={styles.policyCardHeader}>
                  <div className={styles.policyCardIcon}>🏕️</div>
                  <h3 className={styles.policyCardTitle}>Adventure & Activities</h3>
                </div>
                <p className={styles.policyCardText}>
                  Adventure activities including trekking, river rafting, tiger safaris, and hot
                  air balloon rides that are pre-booked cannot be refunded within 72 hours of the
                  scheduled activity. Nature-dependent activities cancelled due to unsafe conditions
                  will be refunded in full or rescheduled at no additional cost.
                </p>
              </div>
            </div>
          </section>

          {/* ── SECTION 3: Refund Processing ── */}
          <section className={styles.section}>
            <div className={styles.sectionHeader}>
              <span className={styles.sectionNumber}>3</span>
              <h2 className={styles.sectionTitle}>Refund Processing Time</h2>
            </div>
            <p className={styles.sectionText}>
              Once a cancellation is approved, refunds are processed according to the following timeline.
              All refunds are returned to the original payment method used at the time of booking.
            </p>

            <div className={styles.refundTimeline}>
              <div className={styles.refundStep}>
                <div className={styles.refundStepLeft}>
                  <div className={styles.refundDot}>1</div>
                  <div className={styles.refundLine} />
                </div>
                <div className={styles.refundStepContent}>
                  <h3 className={styles.refundStepTitle}>Cancellation Request Received</h3>
                  <p className={styles.refundStepText}>
                    You submit a written cancellation request via email to
                    bookings@indianpanorama.in. We will acknowledge receipt within 24 business hours.
                  </p>
                </div>
              </div>

              <div className={styles.refundStep}>
                <div className={styles.refundStepLeft}>
                  <div className={styles.refundDot}>2</div>
                  <div className={styles.refundLine} />
                </div>
                <div className={styles.refundStepContent}>
                  <h3 className={styles.refundStepTitle}>Cancellation Review (1–2 Business Days)</h3>
                  <p className={styles.refundStepText}>
                    Our team reviews your booking, calculates applicable charges, and sends you a
                    formal cancellation confirmation with the refund amount breakdown.
                  </p>
                </div>
              </div>

              <div className={styles.refundStep}>
                <div className={styles.refundStepLeft}>
                  <div className={styles.refundDot}>3</div>
                  <div className={styles.refundLine} />
                </div>
                <div className={styles.refundStepContent}>
                  <h3 className={styles.refundStepTitle}>Supplier Recovery (3–7 Business Days)</h3>
                  <p className={styles.refundStepText}>
                    We contact hotels, airlines, and other service providers to recover refundable
                    amounts on your behalf. This timeline depends on supplier response times.
                  </p>
                </div>
              </div>

              <div className={styles.refundStep}>
                <div className={styles.refundStepLeft}>
                  <div className={styles.refundDot}>4</div>
                  <div className={styles.refundLine} />
                </div>
                <div className={styles.refundStepContent}>
                  <h3 className={styles.refundStepTitle}>Refund Initiation (7–14 Business Days)</h3>
                  <p className={styles.refundStepText}>
                    Once funds are recovered, we process your refund. Bank transfers take 3–5
                    additional business days; card refunds may take 5–7 business days to appear
                    in your account.
                  </p>
                </div>
              </div>

              <div className={styles.refundStep}>
                <div className={styles.refundStepLeft}>
                  <div className={styles.refundDot}>✓</div>
                </div>
                <div className={styles.refundStepContent}>
                  <h3 className={styles.refundStepTitle}>Refund Confirmation</h3>
                  <p className={styles.refundStepText}>
                    You receive an email confirmation once the refund has been successfully
                    processed. Contact us if you have not received your refund within the
                    stated timeframe.
                  </p>
                </div>
              </div>
            </div>

            <div className={styles.greenBox}>
              <p>
                International bank transfers may take up to 10 additional business days due to
                inter-bank processing. Currency conversion rates applicable at the time of refund
                may differ from those at the time of payment.
              </p>
            </div>
          </section>

          {/* ── SECTION 4: No Show ── */}
          <section className={styles.section}>
            <div className={styles.sectionHeader}>
              <span className={styles.sectionNumber}>4</span>
              <h2 className={styles.sectionTitle}>No-Show Policy</h2>
            </div>
            <p className={styles.sectionText}>
              A &ldquo;no-show&rdquo; occurs when a traveller fails to appear for a confirmed service without prior
              notice. In such cases:
            </p>
            <ul className={styles.list}>
              <li className={styles.listItem}>No refund will be provided for the unused portion of any service or tour</li>
              <li className={styles.listItem}>Hotels and transfers reserved for that day will be charged in full</li>
              <li className={styles.listItem}>Subsequent days of the tour may be cancelled without refund at the discretion of Indian Panorama</li>
              <li className={styles.listItem}>If you are delayed due to circumstances beyond your control (flight delay, medical emergency), contact your tour manager immediately — we will do our best to accommodate you</li>
            </ul>
          </section>

          {/* ── SECTION 5: Emergency & Medical ── */}
          <section className={styles.section}>
            <div className={styles.sectionHeader}>
              <span className={styles.sectionNumber}>5</span>
              <h2 className={styles.sectionTitle}>Emergency Situations &amp; Medical Cancellations</h2>
            </div>
            <p className={styles.sectionText}>
              We understand that genuine emergencies happen. In cases of serious illness, bereavement,
              or unavoidable medical circumstances, Indian Panorama will consider cancellation
              requests on compassionate grounds:
            </p>
            <ul className={styles.list}>
              <li className={styles.listItem}>Medical cancellations require a certified medical certificate from a registered physician</li>
              <li className={styles.listItem}>Bereavement cancellations require an appropriate supporting document (death certificate or similar)</li>
              <li className={styles.listItem}>Compassionate cases will be reviewed individually — we may offer a credit note or partial refund at our discretion</li>
              <li className={styles.listItem}>Travel insurance is the primary mechanism for recovering costs in emergency situations — we always advise purchasing cover</li>
            </ul>
            <div className={styles.noteBox}>
              <p>
                <strong>Please Note:</strong> Indian Panorama will always do its best to assist in
                genuine emergency situations, but our ability to provide refunds is limited by the
                policies of our hotels, airlines, and other service partners.
              </p>
            </div>
          </section>

          {/* ── SECTION 6: Force Majeure ── */}
          <section className={styles.section}>
            <div className={styles.sectionHeader}>
              <span className={styles.sectionNumber}>6</span>
              <h2 className={styles.sectionTitle}>Force Majeure Cancellations</h2>
            </div>
            <p className={styles.sectionText}>
              In the event that Indian Panorama cancels your tour due to force majeure circumstances
              (natural disasters, political instability, pandemics, or government-mandated travel
              restrictions), the following applies:
            </p>
            <ul className={styles.list}>
              <li className={styles.listItem}>We will offer alternative travel dates or an equivalent tour as a first preference</li>
              <li className={styles.listItem}>If alternative arrangements are not acceptable, we will issue a full credit note valid for 24 months</li>
              <li className={styles.listItem}>Cash refunds in force majeure situations are subject to recovery from hotels, airlines, and suppliers</li>
              <li className={styles.listItem}>Partial refunds may be issued where some, but not all, services can be recovered</li>
              <li className={styles.listItem}>Indian Panorama will not be liable for any additional costs incurred (flights, visas, travel insurance) outside of the package</li>
            </ul>
          </section>

          {/* ── SECTION 7: How to Cancel ── */}
          <section className={styles.section}>
            <div className={styles.sectionHeader}>
              <span className={styles.sectionNumber}>7</span>
              <h2 className={styles.sectionTitle}>How to Request a Cancellation</h2>
            </div>
            <p className={styles.sectionText}>
              To cancel your booking, please follow these four simple steps. All cancellations must be
              submitted in writing — verbal requests are not accepted.
            </p>
            <div className={styles.stepsGrid}>
              <div className={styles.stepCard}>
                <div className={styles.stepNum}>1</div>
                <h3 className={styles.stepTitle}>Send Written Request</h3>
                <p className={styles.stepText}>
                  Email bookings@indianpanorama.in with your booking reference number and the reason
                  for cancellation.
                </p>
              </div>
              <div className={styles.stepCard}>
                <div className={styles.stepNum}>2</div>
                <h3 className={styles.stepTitle}>Receive Acknowledgement</h3>
                <p className={styles.stepText}>
                  We will confirm receipt within 24 business hours and advise on applicable
                  cancellation charges.
                </p>
              </div>
              <div className={styles.stepCard}>
                <div className={styles.stepNum}>3</div>
                <h3 className={styles.stepTitle}>Review &amp; Approve</h3>
                <p className={styles.stepText}>
                  Our team processes your cancellation and provides a formal breakdown of refundable
                  amounts within 2 business days.
                </p>
              </div>
              <div className={styles.stepCard}>
                <div className={styles.stepNum}>4</div>
                <h3 className={styles.stepTitle}>Refund Processed</h3>
                <p className={styles.stepText}>
                  Approved refunds are returned to your original payment method within 7–14 business
                  days of cancellation approval.
                </p>
              </div>
            </div>
            <div className={styles.greenBox}>
              <p>
                The effective date of cancellation is the date on which Indian Panorama receives and
                acknowledges your written cancellation request, not the date you send it. Please allow
                for email delivery delays when submitting close to a policy deadline.
              </p>
            </div>
          </section>

          {/* ── CTA ── */}
          <div className={styles.contactBlock}>
            <div>
              <h2 className={styles.contactTitle}>Need to Cancel or Modify a Booking?</h2>
              <p className={styles.contactText}>
                Our customer support team is here to help you through the cancellation process and
                explore alternative options wherever possible.
              </p>
            </div>
            <div className={styles.contactLinks}>
              <a href="mailto:bookings@indianpanorama.in" className={styles.contactBtn}>
                Email Bookings Team
              </a>
              <a href="/contact-us" className={styles.contactBtnOutline}>
                Contact Support
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>
  );
}
