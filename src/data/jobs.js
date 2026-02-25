// src/data/jobs.js
export const jobs = Array.from({ length: 200 }, (_, i) => ({
    id: i + 1,
    employerId: i + 1,
    title: i % 2 === 0 ? "Building Labour Required" : "Helper Mason Needed",
    employer: i % 2 === 0 ? "Metro Construction Co." : "Urban Build Pvt Ltd",
    location: i % 2 === 0 ? "Delhi" : "Mumbai",
    address:
        i % 2 === 0
            ? `Plot ${100 + i}, Connaught Place, Delhi`
            : `Building ${200 + i}, Andheri East, Mumbai`,
    pincode: i % 2 === 0 ? "110001" : "400001",
    postedAgo: `${Math.floor(Math.random() * 5) + 1} days ago`,
    salary: `₹ 25000 /month`,
    skillId: i % 2 === 0 ? 1 : 2,
    lat: i % 2 === 0 ? 28.6139 + Math.random() * 0.05 : 19.076 + Math.random() * 0.05,
    lng: i % 2 === 0 ? 77.209 + Math.random() * 0.05 : 72.8777 + Math.random() * 0.05,
    startDate: "2025-11-01",
    endDate: "2025-12-01",
    duration: "1 month",
    description:
        i % 2 === 0
            ? "Assist in general building and construction work."
            : "Help masons in mixing materials and site cleanup.",
    tools: i % 2 === 0 ? ["Hammer", "Trowel"] : ["Mixer", "Bucket"],
    requiredPeople: Math.floor(Math.random() * 5) + 1,
    workTime: "Full Time",
    phone: `98${Math.floor(100000000 + Math.random() * 900000000)}`,
    facilities:
        i % 2 === 0
            ? [
                { icon: "mdi-food", name: "Free Lunch" },
                { icon: "mdi-bed", name: "Accommodation" },
            ]
            : [
                { icon: "mdi-water", name: "Drinking Water" },
                { icon: "mdi-shield-check", name: "Safety Equipment" },
            ],
}));
