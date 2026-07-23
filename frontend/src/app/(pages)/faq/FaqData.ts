export type FaqItem = {
  id: number;
  question: string;
  answer: string;
};

export type FaqCategory = {
  id: string;
  title: string;
  icon: string;
  items: FaqItem[];
};

export const FAQ_CATEGORIES: FaqCategory[] = [
  {
    id: "booking",
    title: "Booking",
    icon: "🗓️",
    items: [
      {
        id: 1,
        question: "How do I book a tour with Indian Panorama?",
        answer:
          "You can book a tour by submitting an enquiry through our website, calling our reservations team, or emailing bookings@indianpanorama.in. Once we understand your travel preferences, our experts will craft a personalised itinerary for your approval. A 25% deposit confirms your booking.",
      },
      {
        id: 2,
        question: "How far in advance should I book my India tour?",
        answer:
          "We recommend booking at least 3–4 months in advance, especially for peak season travel (October–March) and popular destinations like Rajasthan, Kerala, and the Golden Triangle. For holidays that coincide with major Indian festivals such as Diwali, Holi, or Pushkar Fair, booking 6 months ahead is advisable.",
      },
      {
        id: 3,
        question: "Can I customise an existing tour package?",
        answer:
          "Absolutely. All our itineraries are flexible and fully customisable. You can add or remove destinations, upgrade accommodation, change the duration, or include specific experiences such as cooking classes, yoga retreats, or photography tours. Contact our team and we will tailor a package to your exact requirements.",
      },
      {
        id: 4,
        question: "Is a confirmed booking guaranteed?",
        answer:
          "Yes. Once we receive your deposit and send you a written booking confirmation, your itinerary including hotels, transfers, and guides is secured. In the rare event that a specific hotel becomes unavailable, we will arrange equivalent or superior accommodation and inform you promptly.",
      },
    ],
  },
  {
    id: "payments",
    title: "Payments",
    icon: "💳",
    items: [
      {
        id: 5,
        question: "What payment methods does Indian Panorama accept?",
        answer:
          "We accept bank transfers (NEFT/RTGS/SWIFT), credit and debit cards (Visa, Mastercard, American Express), UPI, net banking, and secure online payment gateways (Razorpay and PayPal for international customers). All card transactions are processed through encrypted, PCI-compliant payment systems.",
      },
      {
        id: 6,
        question: "How much deposit is required to confirm a booking?",
        answer:
          "A deposit of 25% of the total tour cost is required at the time of booking to secure your reservation. The remaining 75% balance is due at least 30 days before your departure date. For bookings made within 30 days of departure, full payment is required immediately.",
      },
      {
        id: 7,
        question: "Can I pay in a foreign currency?",
        answer:
          "Yes. We accept payments in major currencies including USD, GBP, EUR, AUD, and CAD. International payments are processed at the prevailing exchange rate at the time of transaction. Any bank charges or conversion fees applicable by your bank are to be borne by the customer.",
      },
      {
        id: 8,
        question: "Are there any hidden charges or taxes?",
        answer:
          "All our tour package prices include applicable GST (Goods and Services Tax) unless explicitly stated otherwise. There are no hidden charges. Any optional extras, personal expenses (tips, laundry, drinks), monument entry fees, or optional excursions are clearly excluded in your itinerary and will be paid directly.",
      },
    ],
  },
  {
    id: "cancellation",
    title: "Cancellation",
    icon: "❌",
    items: [
      {
        id: 9,
        question: "What is Indian Panorama's cancellation policy?",
        answer:
          "Cancellation charges depend on when we receive your written notice: 30+ days before departure — 25% deduction (deposit only); 15–29 days — 50% deduction; 7–14 days — 75% deduction; less than 7 days — no refund. Please refer to our full Cancellation & Refund Policy for complete details.",
      },
      {
        id: 10,
        question: "How do I cancel my booking?",
        answer:
          "All cancellations must be submitted in writing via email to bookings@indianpanorama.in with your booking reference number. Verbal cancellations are not accepted. The date Indian Panorama receives and acknowledges your email is treated as the official cancellation date.",
      },
      {
        id: 11,
        question: "Can I get a refund if I cancel due to illness?",
        answer:
          "In cases of serious illness, Indian Panorama will consider compassionate refunds upon receipt of a certified medical certificate. However, our ability to refund depends on supplier recovery. This is exactly why we strongly recommend purchasing comprehensive travel insurance that covers trip cancellation due to medical reasons.",
      },
    ],
  },
  {
    id: "hotels",
    title: "Hotels",
    icon: "🏨",
    items: [
      {
        id: 12,
        question: "What types of accommodation does Indian Panorama offer?",
        answer:
          "We offer accommodation across all categories — from 5-star luxury city hotels and heritage palaces to boutique eco-lodges, wildlife camps, and traditional havelis. We specialise in carefully curated properties that reflect the authentic character of each destination, whether it's a palace hotel in Udaipur or a houseboat in Kerala.",
      },
      {
        id: 13,
        question: "When is hotel check-in and check-out?",
        answer:
          "Standard check-in is at 14:00 (2 PM) and check-out is at 12:00 (noon). Early check-in or late check-out can often be arranged subject to availability and may incur additional charges. Please inform us of any specific timing requirements at the time of booking so we can try to accommodate your needs.",
      },
      {
        id: 14,
        question: "Can I request a specific room type or preference?",
        answer:
          "Yes. We can note room preferences such as non-smoking rooms, high floors, pool views, twin beds, or disability-accessible rooms. While we request these preferences from hotels, they are subject to availability at the time of check-in and cannot be guaranteed.",
      },
    ],
  },
  {
    id: "flights",
    title: "Flights",
    icon: "✈️",
    items: [
      {
        id: 15,
        question: "Do Indian Panorama tour packages include international flights?",
        answer:
          "Most of our tour packages are land-only and do not include international flights, allowing you to choose your preferred airline and fare class. We can assist with domestic flights within India, and upon request, we can source competitive international flight quotes through our airline partners.",
      },
      {
        id: 16,
        question: "What is the baggage allowance on Indian domestic flights?",
        answer:
          "Baggage allowances vary by airline and fare class. Budget carriers like IndiGo and SpiceJet typically allow 15–20 kg checked baggage and 7 kg cabin baggage. Full-service carriers like Air India and Vistara allow 23–25 kg checked. Always verify with the specific airline before travel.",
      },
      {
        id: 17,
        question: "What happens if my flight is delayed and I miss a connection?",
        answer:
          "If a flight delay causes you to miss a tour connection, contact your Indian Panorama tour manager immediately. We will do our best to rearrange transfers and adjust the itinerary at no extra charge if the delay is caused by the airline. Travel insurance covering missed connections is strongly advised.",
      },
    ],
  },
  {
    id: "visa",
    title: "Visa",
    icon: "🛂",
    items: [
      {
        id: 18,
        question: "Do I need a visa to travel to India?",
        answer:
          "Most international visitors require a visa to enter India. Citizens of over 160 countries are eligible for India's e-Visa (eTV), which can be applied for online up to 4 days before arrival. Some nationalities require a traditional embassy visa. We strongly recommend checking current requirements with the Indian Embassy or High Commission in your country.",
      },
      {
        id: 19,
        question: "Can Indian Panorama assist with my visa application?",
        answer:
          "We provide a visa support letter confirming your tour booking, which can be submitted as part of your visa application. We can also guide you through the e-Visa application process. However, Indian Panorama is not a visa processing agency and cannot guarantee visa approval. Visa fees and processing are the customer's responsibility.",
      },
      {
        id: 20,
        question: "How long before travel should I apply for an Indian visa?",
        answer:
          "For the India e-Visa, we recommend applying at least 2 weeks before travel (applications open 120 days before arrival). Traditional embassy visas may take 4–8 weeks depending on your country. Do not purchase non-refundable flights until your visa is approved.",
      },
    ],
  },
  {
    id: "transportation",
    title: "Transportation",
    icon: "🚗",
    items: [
      {
        id: 21,
        question: "What type of vehicles are used for transfers and tours?",
        answer:
          "We use clean, well-maintained, air-conditioned vehicles appropriate for your group size and destination. Options include premium sedans (Toyota Innova, Mahindra XUV), mini-coaches, and full-size coaches for larger groups. For mountain terrain, we use robust SUVs. All vehicles and drivers are fully licensed and insured.",
      },
      {
        id: 22,
        question: "Can I request a private driver for my entire tour?",
        answer:
          "Yes. All our private tour packages include a dedicated driver and vehicle for the duration of your itinerary. Your driver will be familiar with your destinations, assist with luggage, and be available throughout the day as per the itinerary. Drivers speak basic English and are experienced with tourist requirements.",
      },
    ],
  },
  {
    id: "safety",
    title: "Safety",
    icon: "🛡️",
    items: [
      {
        id: 23,
        question: "Is India safe for solo travellers and women?",
        answer:
          "India is generally safe for tourists when travelling with a reputable operator. Indian Panorama provides 24/7 emergency support, vetted guides and drivers, and carefully selected accommodation. We advise all travellers to exercise standard precautions: avoid displaying valuables, use hotel safes, and stay in well-lit, populated areas at night. Our team will brief you on specific safety tips for each destination.",
      },
      {
        id: 24,
        question: "What medical facilities are available in India?",
        answer:
          "Major Indian cities (Delhi, Mumbai, Bangalore, Chennai) have world-class private hospitals with international-standard care. Smaller towns have government hospitals and private clinics. Remote destinations may have limited medical facilities. We strongly recommend travel insurance with medical evacuation cover and carrying a basic first aid kit and any personal prescription medications.",
      },
    ],
  },
  {
    id: "tour-packages",
    title: "Tour Packages",
    icon: "🗺️",
    items: [
      {
        id: 25,
        question: "What is typically included in an Indian Panorama tour package?",
        answer:
          "Our packages typically include: accommodation on a specified meal plan (Bed & Breakfast, Half Board, or Full Board), all ground transportation in air-conditioned vehicles, airport and station transfers, the services of a professional English-speaking guide at each destination, and all activities listed in the itinerary. Flights, visas, personal expenses, tips, and optional excursions are generally excluded.",
      },
      {
        id: 26,
        question: "Do you offer group tours or only private tours?",
        answer:
          "Indian Panorama specialises in private bespoke tours, meaning you travel with only your own party and have complete flexibility. We also offer small-group fixed departures for solo travellers or those who prefer to travel with like-minded individuals. Group sizes for fixed departures are typically capped at 12–16 people.",
      },
    ],
  },
  {
    id: "customization",
    title: "Customisation",
    icon: "✏️",
    items: [
      {
        id: 27,
        question: "How do I start planning a customised trip to India?",
        answer:
          "Simply fill in our online enquiry form or email us with your travel dates, group size, budget, and interests. One of our destination specialists will contact you within 24 hours to discuss your vision. We will then prepare a detailed bespoke itinerary with accommodation options at multiple budget levels for you to review and refine.",
      },
      {
        id: 28,
        question: "Can I combine multiple Indian states in one trip?",
        answer:
          "Absolutely. India is vast and rewarding — combining regions is one of the joys of touring here. Popular combinations include the Golden Triangle with Rajasthan, South India combining Kerala and Tamil Nadu, or North and Northeast India. We will advise on realistic timings so you don't feel rushed between destinations.",
      },
    ],
  },
  {
    id: "children",
    title: "Children",
    icon: "👶",
    items: [
      {
        id: 29,
        question: "Are Indian Panorama tours suitable for families with children?",
        answer:
          "Yes, we love welcoming families with children! We design family-friendly itineraries with appropriate activity levels, child-friendly accommodation, and a pacing that suits younger travellers. We can arrange child seats in vehicles, suggest age-appropriate experiences, and accommodate dietary preferences for children.",
      },
      {
        id: 30,
        question: "Are there discounts for children on tour packages?",
        answer:
          "Children under 5 years old generally travel free of charge (sharing existing bedding with parents). Children aged 5–11 receive discounts of 25–40% on land packages. Exact discounts depend on the specific itinerary and hotel policies. Flights and some experiences may be priced differently. Please specify children's ages when requesting a quote.",
      },
    ],
  },
  {
    id: "senior",
    title: "Senior Citizens",
    icon: "🧓",
    items: [
      {
        id: 31,
        question: "Do you offer tours suitable for senior travellers?",
        answer:
          "Yes. We regularly plan tours for senior travellers with a focus on comfort, manageable pace, and superior accommodation. We can arrange wheelchair assistance at airports, ground-floor hotel rooms, extra rest stops during drives, and skip-the-queue entry at monuments. Our senior-friendly itineraries avoid excessive walking and physical exertion while still delivering incredible experiences.",
      },
    ],
  },
  {
    id: "meals",
    title: "Meals",
    icon: "🍛",
    items: [
      {
        id: 32,
        question: "Can Indian Panorama accommodate dietary restrictions?",
        answer:
          "Yes. We accommodate vegetarian, vegan, gluten-free, Jain, halal, and other dietary requirements. Please inform us of all dietary restrictions at the time of booking so we can brief hotels and guides accordingly. India offers an extraordinary range of vegetarian and vegan food, making it a wonderful destination for plant-based travellers.",
      },
    ],
  },
  {
    id: "weather",
    title: "Weather",
    icon: "🌤️",
    items: [
      {
        id: 33,
        question: "What is the best time to visit India?",
        answer:
          "The best time varies by region. For most of India (Rajasthan, Delhi, Agra, Kerala, Tamil Nadu), October to March is ideal with pleasant temperatures. The Himalayas are best visited May–June and September–October. Monsoon season (June–September) transforms the landscape and offers lush greenery with fewer crowds. We can help you choose the best time based on your specific destinations.",
      },
    ],
  },
  {
    id: "insurance",
    title: "Travel Insurance",
    icon: "🛡️",
    items: [
      {
        id: 34,
        question: "Is travel insurance mandatory for booking with Indian Panorama?",
        answer:
          "Travel insurance is mandatory for all international visitors booking through Indian Panorama. We require proof of insurance before confirming your tour. A comprehensive policy should cover medical emergencies (including evacuation), trip cancellation, lost baggage, and flight delays. We strongly recommend purchasing insurance as soon as you confirm your booking.",
      },
      {
        id: 35,
        question: "Does travel insurance cover monsoon-related disruptions?",
        answer:
          "Most comprehensive travel insurance policies cover trip cancellations or curtailments resulting from extreme weather events such as floods or cyclones if they occur after the policy purchase date. Weather-related disruptions that were foreseeable or already occurring at the time of purchase are generally not covered. Always read your policy wording carefully and purchase insurance early.",
      },
    ],
  },
];
