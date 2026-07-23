"use client";

import DatePicker from "react-datepicker";
import "react-datepicker/dist/react-datepicker.css";
import "./TravelDatePicker.css";

// Sab enquiry forms ka shared travel-date input — custom calendar popup (jQuery-style,
// react-datepicker se), past dates aur agle 2 din (aaj + kal) select nahi ho sakte.

function getMinSelectableDate(): Date {
  const d = new Date();
  d.setHours(0, 0, 0, 0);
  d.setDate(d.getDate() + 2);
  return d;
}

interface Props {
  name: string;
  value: string; // "YYYY-MM-DD" ya ""
  onChange: (name: string, value: string) => void;
  placeholder?: string;
  className?: string;
}

function toDate(value: string): Date | null {
  if (!value) return null;
  const d = new Date(value + "T00:00:00");
  return Number.isNaN(d.getTime()) ? null : d;
}

function toIsoDate(d: Date): string {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, "0");
  const day = String(d.getDate()).padStart(2, "0");
  return `${y}-${m}-${day}`;
}

export default function TravelDatePicker({ name, value, onChange, placeholder = "Travel date*", className }: Props) {
  return (
    <DatePicker
      selected={toDate(value)}
      onChange={(date: Date | null) => onChange(name, date ? toIsoDate(date) : "")}
      minDate={getMinSelectableDate()}
      placeholderText={placeholder}
      dateFormat="dd MMM yyyy"
      className={className}
      wrapperClassName="travelDatePickerWrapper"
      autoComplete="off"
      required
    />
  );
}
