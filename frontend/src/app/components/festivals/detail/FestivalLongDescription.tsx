import ReadMoreHtml from "../../common/ReadMoreHtml";
import styles from "./FestivalLongDescription.module.css";

interface Props {
  html: string;
}

export default function FestivalLongDescription({ html }: Props) {
  if (!html) return null;

  return (
    <section>
      <ReadMoreHtml html={html} className={styles.content} lines={8} />
    </section>
  );
}
