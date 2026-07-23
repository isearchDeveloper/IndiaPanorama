"use client";

import { useState } from "react";
import Image from "next/image";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay, Pagination } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";

import { RiUserLine, RiCarLine, RiBusLine } from "react-icons/ri";
import { MdOutlineLocalGasStation } from "react-icons/md";
import styles from "./CarRental.module.css";

const tabs = ["South India", "North India", "East India", "West India", "Central India"];

// Dummy data covering all tabs
const carsData: Record<string, any[]> = {
  "South India": [
    { id: 1, name: "Maruti Ciaz", passengers: "04", fuel: "Petrol", type: "Sedan", image: "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=600&q=80" },
    { id: 2, name: "Maruti Ertiga", passengers: "04", fuel: "Petrol", type: "Sedan", image: "https://images.unsplash.com/photo-1469285994282-454ceb49e63c?auto=format&fit=crop&w=600&q=80" },
    { id: 3, name: "Tempo Traveller", passengers: "11", fuel: "Petrol", type: "Traveller", image: "https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=600&q=80" },
    { id: 4, name: "Toyota Innova Crysta", passengers: "06", fuel: "Petrol", type: "SUV", image: "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=600&q=80" },
    { id: 5, name: "Hyundai Verna", passengers: "04", fuel: "Diesel", type: "Sedan", image: "https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=600&q=80" },
    { id: 6, name: "Mahindra XUV700", passengers: "07", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=600&q=80" },
  ],
  "North India": [
    { id: 1, name: "Toyota Fortuner", passengers: "07", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=600&q=80" },
    { id: 2, name: "Honda City", passengers: "04", fuel: "Petrol", type: "Sedan", image: "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=600&q=80" },
    { id: 3, name: "Innova Crysta", passengers: "06", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1469285994282-454ceb49e63c?auto=format&fit=crop&w=600&q=80" },
    { id: 4, name: "Force Traveller", passengers: "12", fuel: "Diesel", type: "Traveller", image: "https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=600&q=80" },
  ],
  "East India": [
    { id: 1, name: "Mahindra Scorpio", passengers: "07", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=600&q=80" },
    { id: 2, name: "Maruti Dzire", passengers: "04", fuel: "Petrol", type: "Sedan", image: "https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=600&q=80" },
    { id: 3, name: "Toyota Innova", passengers: "06", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=600&q=80" },
    { id: 4, name: "Tata Safari", passengers: "07", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1469285994282-454ceb49e63c?auto=format&fit=crop&w=600&q=80" },
  ],
  "West India": [
    { id: 1, name: "Kia Carens", passengers: "06", fuel: "Petrol", type: "SUV", image: "https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=600&q=80" },
    { id: 2, name: "Hyundai Creta", passengers: "04", fuel: "Petrol", type: "SUV", image: "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=600&q=80" },
    { id: 3, name: "Maruti Ertiga", passengers: "06", fuel: "CNG", type: "Sedan", image: "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=600&q=80" },
    { id: 4, name: "Tempo Traveller", passengers: "14", fuel: "Diesel", type: "Traveller", image: "https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=600&q=80" },
  ],
  "Central India": [
    { id: 1, name: "Toyota Innova Crysta", passengers: "06", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=600&q=80" },
    { id: 2, name: "Honda Amaze", passengers: "04", fuel: "Petrol", type: "Sedan", image: "https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&w=600&q=80" },
    { id: 3, name: "Mahindra Bolero", passengers: "07", fuel: "Diesel", type: "SUV", image: "https://images.unsplash.com/photo-1563720223185-11003d516935?auto=format&fit=crop&w=600&q=80" },
    { id: 4, name: "Force Urbania", passengers: "12", fuel: "Diesel", type: "Traveller", image: "https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=600&q=80" },
  ]
};

export default function CarRental() {
  const [activeTab, setActiveTab] = useState("South India");
  const activeCars = carsData[activeTab] || [];

  return (
    <section className={styles.section}>
      <div className={styles.container}>
        <h2 className={styles.heading}>Car Rental</h2>

        {/* Tabs */}
        <div className={styles.tabsContainer}>
          {tabs.map((tab) => (
            <button
              key={tab}
              className={`${styles.tab} ${activeTab === tab ? styles.activeTab : ""}`}
              onClick={() => setActiveTab(tab)}
            >
              {tab}
            </button>
          ))}
        </div>

        {/* JS-off fallback: show all tabs statically */}
        <noscript>
          {tabs.map((tab) => (
            <div key={tab} style={{ marginBottom: 32 }}>
              <h3 style={{ fontSize: 16, fontWeight: 700, marginBottom: 12 }}>{tab}</h3>
              <div className="noscript-grid">
                {(carsData[tab] || []).map((car) => (
                  <div key={car.id} className={styles.card}>
                    <div className={styles.imageWrapper}>
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img src={car.image} alt={car.name} />
                    </div>
                    <div className={styles.cardContent}>
                      <h3 className={styles.carName}>{car.name}</h3>
                      <div className={styles.cardFooter}>
                        <div className={styles.features}>
                          <span className={styles.feature}>{car.passengers} Passengers</span>
                          <span className={styles.feature}>{car.fuel}</span>
                          <span className={styles.feature}>{car.type}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </noscript>

        {/* Swiper Carousel */}
        <Swiper
          className={styles.swiperContainer}
          modules={[Autoplay, Pagination]}
          spaceBetween={30}
          slidesPerView={1}
          pagination={{ clickable: true, dynamicBullets: true }}
          autoplay={{ delay: 3000, disableOnInteraction: false }} // Added autoplay so it scrolls naturally
          breakpoints={{
            640: { slidesPerView: 2 },
            992: { slidesPerView: 3 },
            1200: { slidesPerView: 4 },
          }}
        >
          {activeCars.map((car) => (
            <SwiperSlide key={car.id}>
              <div className={styles.card}>
                <div className={styles.imageWrapper}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={car.image} alt={car.name} />
                </div>
                <div className={styles.cardContent}>
                  <h3 className={styles.carName}>{car.name}</h3>
                  <div className={styles.cardFooter}>
                    <div className={styles.features}>
                      <span className={styles.feature}>
                        <RiUserLine className={styles.featureIcon} /> {car.passengers}
                      </span>
                      <span className={styles.feature}>
                        <MdOutlineLocalGasStation className={styles.featureIcon} /> {car.fuel}
                      </span>
                      <span className={styles.feature}>
                        {car.type === "Traveller" ? (
                          <RiBusLine className={styles.featureIcon} />
                        ) : (
                          <RiCarLine className={styles.featureIcon} />
                        )}{" "}
                        {car.type}
                      </span>
                    </div>
                    <button className={styles.bookBtn}>Book Now</button>
                  </div>
                </div>
              </div>
            </SwiperSlide>
          ))}
        </Swiper>
      </div>
    </section>
  );
}
