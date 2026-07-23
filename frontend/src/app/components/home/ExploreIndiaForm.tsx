"use client";

import { useState, useCallback } from "react";
import { useRouter } from "next/navigation";
import Image from "next/image";
import Recaptcha from "@/app/components/common/Recaptcha";
import TravelDatePicker from "@/app/components/common/TravelDatePicker";
import { submitEnquiry } from "@/services/enquiryService";
import styles from "./ExploreIndiaForm.module.css";

export default function ExploreIndiaForm() {
  const router = useRouter();
  const [formData, setFormData] = useState({
    fullName: "",
    email: "",
    phoneNumber: "",
    countryName: "",
    hotel: "",
    budget: "",
    noOfPersons: "",
    travelDate: "",
    cities: "",
    requirements: ""
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const [submitting, setSubmitting] = useState(false);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  const [captchaToken, setCaptchaToken] = useState<string | null>(null);
  const [captchaError, setCaptchaError] = useState(false);
  const [resetKey, setResetKey] = useState(0);

  const handleSubmit = useCallback(async (e: React.FormEvent) => {
    e.preventDefault();
    if (!captchaToken) {
      setCaptchaError(true);
      return;
    }
    setCaptchaError(false);
    setErrorMsg(null);
    setSubmitting(true);

    const result = await submitEnquiry({
      name: formData.fullName,
      email: formData.email,
      phone: formData.phoneNumber,
      country: formData.countryName,
      budget: formData.budget,
      no_of_persons: formData.noOfPersons,
      travel_date: formData.travelDate,
      arrival_city: formData.cities,
      message: formData.requirements,
      captchaToken,
    });

    setSubmitting(false);

    if (result.success) {
      router.push("/thank-you");
    } else {
      setErrorMsg("Something went wrong. Please try again.");
      setCaptchaToken(null);
      setResetKey((k) => k + 1);
    }
  }, [captchaToken, formData, router]);

  return (
    <section className={styles.section}>
      <div className={styles.container}>
      {/* Left decorative image inside container for perfect relative alignment */}
      <Image
        src="/images/watercolor-illustration-woman-weaving-with-traditional-indian-dress%201left.png"
        alt="Woman weaving"
        width={180}
        height={300}
        className={styles.leftImage}
        aria-hidden="true"
      />

      {/* Right decorative image inside container for perfect relative alignment */}
      {/* unoptimized: Next.js isko re-compress nahi karega — original file jaisi hai waisi hi serve hogi */}
      <Image
        src="/images/new-image.webp"
        alt="Woman creating rangoli"
        width={180}
        height={300}
        className={styles.rightImage}
        aria-hidden="true"
        unoptimized
      />

        <div className={styles.formCard}>
          <h2 className={styles.heading}>Explore India and Indian Culture With us!</h2>

          <form onSubmit={handleSubmit} className={styles.form}>
            {/* Row 1: Full name, Email, Phone number */}
            <div className={styles.row3}>
              <div className={styles.inputGroup}>
                <input
                  type="text"
                  name="fullName"
                  placeholder="Full name*"
                  required
                  value={formData.fullName}
                  onChange={handleChange}
                  className={styles.input}
                />
              </div>

              <div className={styles.inputGroup}>
                <input
                  type="email"
                  name="email"
                  placeholder="@ Your email*"
                  required
                  value={formData.email}
                  onChange={handleChange}
                  className={styles.input}
                />
              </div>

              <div className={styles.phoneInputWrapper}>
                <div className={styles.countryCode}>
                  <img
                    src="https://flagcdn.com/w20/in.png"
                    srcSet="https://flagcdn.com/w40/in.png 2x"
                    alt="India"
                  />
                  <svg
                    stroke="currentColor"
                    fill="currentColor"
                    strokeWidth="0"
                    viewBox="0 0 24 24"
                    height="1em"
                    width="1em"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path d="M7 10l5 5 5-5z"></path>
                  </svg>
                </div>
                <input
                  type="tel"
                  name="phoneNumber"
                  placeholder="Phone number*"
                  required
                  value={formData.phoneNumber}
                  onChange={handleChange}
                  className={styles.phoneInput}
                />
              </div>
            </div>

            {/* Row 2: Country name, Hotel, Budget */}
            <div className={styles.row3}>
              <div className={styles.inputGroup}>
                <input
                  type="text"
                  name="countryName"
                  placeholder="Country name*"
                  required
                  value={formData.countryName}
                  onChange={handleChange}
                  className={styles.input}
                />
              </div>

              <div className={styles.inputGroup}>
                <select
                  name="hotel"
                  value={formData.hotel}
                  onChange={handleChange}
                  className={styles.input}
                >
                  <option value="" disabled>
                    Hotel
                  </option>
                  <option value="5-star">5 Star Luxury</option>
                  <option value="4-star">4 Star Premium</option>
                  <option value="3-star">3 Star Standard</option>
                  <option value="heritage">Heritage Hotels</option>
                  <option value="budget">Homestays / Budget</option>
                </select>
              </div>

              <div className={styles.inputGroup}>
                <input
                  type="text"
                  name="budget"
                  placeholder="Budget*"
                  required
                  value={formData.budget}
                  onChange={handleChange}
                  className={styles.input}
                />
              </div>
            </div>

            {/* Row 3: No. of persons, Date and duration */}
            <div className={styles.row2}>
              <div className={styles.inputGroup}>
                <input
                  type="text"
                  name="noOfPersons"
                  placeholder="No. of persons*"
                  required
                  value={formData.noOfPersons}
                  onChange={handleChange}
                  className={styles.input}
                />
              </div>

              <div className={styles.inputGroup}>
                <TravelDatePicker
                  name="travelDate"
                  value={formData.travelDate}
                  onChange={(name, value) => setFormData((prev) => ({ ...prev, [name]: value }))}
                  className={styles.input}
                />
              </div>
            </div>

            {/* Row 4: Arrival and departure city in india */}
            <div className={styles.row1}>
              <div className={styles.inputGroup}>
                <input
                  type="text"
                  name="cities"
                  placeholder="Arrival and departure city in india*"
                  required
                  value={formData.cities}
                  onChange={handleChange}
                  className={styles.input}
                />
              </div>
            </div>

            {/* Row 5: Travel Requirements textarea */}
            <div className={styles.row1}>
              <div className={styles.inputGroup}>
                <textarea
                  name="requirements"
                  placeholder="Travel Requirements...*"
                  required
                  value={formData.requirements}
                  onChange={handleChange}
                  className={styles.input}
                />
              </div>
            </div>

            {/* Human verification — Google reCAPTCHA */}
            <div className={styles.row1}>
              <Recaptcha onChange={setCaptchaToken} resetKey={resetKey} />
              {captchaError && (
                <p className={styles.errorMsg}>Please verify that you are not a robot.</p>
              )}
            </div>

            {/* Submit Button */}
            <div className={styles.submitWrapper}>
              <button type="submit" className={styles.submitBtn} disabled={submitting}>
                {submitting ? "Sending..." : "Enquire Now"}
              </button>
              {errorMsg && <p className={styles.errorMsg}>{errorMsg}</p>}
            </div>
          </form>
        </div>
      </div>
    </section>
  );
}
