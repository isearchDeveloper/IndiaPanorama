"use client";

import { useState, useCallback } from "react";
import { useRouter } from "next/navigation";
import Recaptcha from "./Recaptcha";
import TravelDatePicker from "./TravelDatePicker";
import { submitEnquiry } from "@/services/enquiryService";
import styles from "./SidebarForm.module.css";

export default function SidebarForm() {
  const router = useRouter();
  const [formData, setFormData] = useState({
    fullName: "", email: "", phone: "", country: "",
    hotel: "", budget: "", persons: "", travelDate: "",
    cities: "", requirements: "",
  });

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>
  ) => {
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
      phone: formData.phone,
      country: formData.country,
      budget: formData.budget,
      no_of_persons: formData.persons,
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
    <div className={styles.sidebarWrapper}>

      {/* Enquiry Form Card */}
      <div className={styles.formCard}>
        <h3 className={styles.formHeading}>Explore India and Indian Culture With us!</h3>
        <form onSubmit={handleSubmit} className={styles.form}>
          <input name="fullName" type="text" placeholder="Full name*" required
            value={formData.fullName} onChange={handleChange} className={styles.input} />
          <input name="email" type="email" placeholder="@ Your email*" required
            value={formData.email} onChange={handleChange} className={styles.input} />
          <div className={styles.phoneRow}>
            <div className={styles.flagBox}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src="https://flagcdn.com/w20/in.png" alt="IN" width={20} />
              <span className={styles.flagChevron}>▾</span>
            </div>
            <input name="phone" type="tel" placeholder="Phone number*" required
              value={formData.phone} onChange={handleChange} className={styles.phoneInput} />
          </div>
          <input name="country" type="text" placeholder="Country name*" required
            value={formData.country} onChange={handleChange} className={styles.input} />
          <select name="hotel" value={formData.hotel} onChange={handleChange} className={styles.input}>
            <option value="" disabled>Hotel</option>
            <option value="5-star">5 Star Luxury</option>
            <option value="4-star">4 Star Premium</option>
            <option value="3-star">3 Star Standard</option>
            <option value="heritage">Heritage Hotels</option>
            <option value="budget">Homestays / Budget</option>
          </select>
          <input name="budget" type="text" placeholder="Budget*" required
            value={formData.budget} onChange={handleChange} className={styles.input} />
          <div className={styles.row2}>
            <input name="persons" type="text" placeholder="No. of persons*" required
              value={formData.persons} onChange={handleChange} className={styles.input} />
            <TravelDatePicker
              name="travelDate"
              value={formData.travelDate}
              onChange={(name, value) => setFormData((prev) => ({ ...prev, [name]: value }))}
              className={styles.input}
            />
          </div>
          <input name="cities" type="text" placeholder="Arrival and departure city in india*" required
            value={formData.cities} onChange={handleChange} className={styles.input} />
          <textarea name="requirements" placeholder="Travel Requirements...*" required
            value={formData.requirements} onChange={handleChange}
            className={styles.textarea} rows={3} />

          {/* Human verification — Google reCAPTCHA */}
          <Recaptcha onChange={setCaptchaToken} resetKey={resetKey} />
          {captchaError && (
            <p className={styles.errorMsg}>Please verify that you are not a robot.</p>
          )}

          <button type="submit" className={styles.submitBtn} disabled={submitting}>
            {submitting ? "Sending..." : "Enquire Now"}
          </button>
          {errorMsg && <p className={styles.errorMsg}>{errorMsg}</p>}
        </form>
      </div>

    </div>
  );
}
