import Link from "next/link";
import styles from "./PageNotFound.module.css";

interface PageNotFoundProps {
  heading?: string;
  message?: string;
  backLabel?: string;
  backHref?: string;
}

export default function PageNotFound({
  heading = "Page Not Found",
  message = "The page you're looking for doesn't exist or has been moved.",
  backLabel = "Back to Home",
  backHref = "/",
}: PageNotFoundProps) {
  return (
    // data-page-not-found: PopularPackagesGate isse dekh ke 404 pe khud hide hota hai
    <div className={styles.wrapper} data-page-not-found="">
      <div className={styles.inner}>
        <p className={styles.code} aria-hidden="true">404</p>
        <h1 className={styles.heading}>{heading}</h1>
        <p className={styles.message}>{message}</p>
        <Link href={backHref} className={styles.btn}>
          {backLabel}
        </Link>
      </div>
    </div>
  );
}
