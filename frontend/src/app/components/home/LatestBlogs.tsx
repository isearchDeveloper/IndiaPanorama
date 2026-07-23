"use client";

import { RiArrowRightUpLine, RiUserLine, RiCalendar2Line, RiEyeLine } from "react-icons/ri";
import Link from "next/link";
import { Swiper, SwiperSlide } from "swiper/react";
import { Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import SafeImage from "@/app/components/common/SafeImage";
import type { HomeData } from "@/services/homeService";
import styles from "./LatestBlogs.module.css";

interface Props {
  data: HomeData["latest_blogs"] | null;
}

export default function LatestBlogs({ data }: Props) {
  if (!data || !data.blogs.length) return null;

  return (
    <section className={styles.section}>
      <div className={styles.container}>

        <div className={styles.header}>
          <h2 className={styles.title}>{data.title}</h2>
          <div className={styles.descriptionBlock}>
            <p className={styles.description}>{data.subtitle}</p>
            <Link href={data.button_url || "/blog"} className={styles.viewAll}>
              {data.button_text || "View All Blogs"} <RiArrowRightUpLine />
            </Link>
          </div>
        </div>

        <Swiper
          modules={[Pagination, Autoplay]}
          spaceBetween={30}
          slidesPerView={3}
          pagination={{ clickable: true, el: `.${styles.pagination}`, dynamicBullets: true }}
          autoplay={{ delay: 4000, disableOnInteraction: false, pauseOnMouseEnter: true }}
          loop={data.blogs.length > 3}
          breakpoints={{
            0:   { slidesPerView: 1, spaceBetween: 16 },
            640: { slidesPerView: 2, spaceBetween: 20 },
            991: { slidesPerView: 3, spaceBetween: 30 },
          }}
          className={styles.swiper}
        >
          {data.blogs.map((blog, index) => (
            <SwiperSlide key={blog.id ?? index} className={styles.slide}>
              <Link href={blog.url || `/blog/${blog.slug}`} className={styles.card}>

                <div className={styles.imageWrapper}>
                  <SafeImage
                    src={blog.image}
                    alt={blog.image_alt || blog.title}
                    fill
                    sizes="(max-width: 640px) 100vw, (max-width: 991px) 50vw, 33vw"
                    className="object-cover"
                  />
                  <div className={styles.arrowBtn}>
                    <RiArrowRightUpLine size={18} />
                  </div>
                </div>

                <div className={styles.cardBody}>
                  <div className={styles.meta}>
                    {blog.published_at && (
                      <div className={styles.metaItem}>
                        <RiCalendar2Line className={styles.metaIcon} />
                        <span>{blog.published_at}</span>
                      </div>
                    )}
                    {blog.author && (
                      <div className={styles.metaItem}>
                        <RiUserLine className={styles.metaIcon} />
                        <span>{blog.author}</span>
                      </div>
                    )}
                  </div>
                  <h3 className={styles.blogTitle}>{blog.title}</h3>
                  {blog.views && (
                    <div className={styles.views}>
                      <RiEyeLine className={styles.metaIcon} />
                      <span>{blog.views} views</span>
                    </div>
                  )}
                </div>

              </Link>
            </SwiperSlide>
          ))}
        </Swiper>

        <div className={styles.pagination} />

      </div>
    </section>
  );
}
