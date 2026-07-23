import Breadcrumb from "./Breadcrumb";
import type { BreadcrumbItem } from "./Breadcrumb";
import styles from "./PageLayout.module.css";

interface Props {
  breadcrumb: BreadcrumbItem[];
  sidebar?: React.ReactNode;
  children: React.ReactNode;
  fullWidth?: boolean; // no sidebar, full width
}

export default function PageLayout({ breadcrumb, sidebar, children, fullWidth }: Props) {
  return (
    <div className={styles.outer}>
      <div className={fullWidth ? styles.containerFull : styles.container}>
        <Breadcrumb items={breadcrumb} />
        <div className={sidebar && !fullWidth ? styles.twoCol : styles.oneCol}>
          <main className={styles.main}>{children}</main>
          {sidebar && !fullWidth && (
            <aside className={styles.sidebar} aria-label="Enquiry form">
              {sidebar}
            </aside>
          )}
        </div>
      </div>
    </div>
  );
}
