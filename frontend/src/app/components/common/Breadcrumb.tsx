import Link from "next/link";
import styles from "./Breadcrumb.module.css";

export interface BreadcrumbItem {
  label: string;
  href: string;
}

interface Props {
  items: BreadcrumbItem[];
}

export default function Breadcrumb({ items }: Props) {
  return (
    <nav className={styles.nav} aria-label="Breadcrumb">
      <ol className={styles.list}>
        {items.map((item, i) => {
          const isLast = i === items.length - 1;
          return (
            <li key={item.href} className={styles.item}>
              {isLast ? (
                <span className={styles.current} aria-current="page">{item.label}</span>
              ) : (
                <>
                  <Link href={item.href} className={styles.link}>{item.label}</Link>
                  <span className={styles.sep} aria-hidden="true">/</span>
                </>
              )}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}
