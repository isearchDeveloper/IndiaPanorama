"use client";

import { useState } from "react";
import Image, { ImageProps } from "next/image";
import { ImageOff } from "lucide-react";
import styles from "./SafeImage.module.css";

type SafeImageProps = Omit<ImageProps, "src" | "alt"> & {
  src?: string | null;
  alt?: string;
  placeholderClassName?: string;
};

export default function SafeImage({
  src,
  alt = "",
  className,
  placeholderClassName,
  ...rest
}: SafeImageProps) {
  const [broken, setBroken] = useState(false);

  if (!src || broken) {
    const { fill, width, height } = rest;
    const sizeStyle =
      !fill && width && height
        ? { width: Number(width), height: Number(height) }
        : undefined;

    return (
      <div
        className={`${styles.placeholder} ${fill ? styles.fill : ""} ${
          placeholderClassName ?? className ?? ""
        }`}
        style={sizeStyle}
        role="img"
        aria-label={alt || "Image not available"}
      >
        <ImageOff size={28} strokeWidth={1.5} aria-hidden="true" />
      </div>
    );
  }

  return (
    <Image
      src={src}
      alt={alt}
      className={className}
      onError={() => setBroken(true)}
      {...rest}
    />
  );
}
