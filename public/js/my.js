document.addEventListener("DOMContentLoaded", function () {
    const scrollLinks = [
        { id: "scroll-ke-home", target: "#scroll-home" },
        { id: "scroll-ke-home-2", target: "#scroll-home" },
        { id: "scroll-ke-home-3", target: "#scroll-home" },
        { id: "scroll-ke-grup-jadwal", target: "#grup-jadwal" },
        { id: "scroll-ke-grup-jadwal-2", target: "#grup-jadwal" },
        { id: "scroll-ke-grup-jadwal-3", target: "#grup-jadwal" },
        { id: "scroll-ke-grup-jadwal-4", target: "#grup-jadwal" },
        { id: "scroll-ke-jadwal-sholat", target: "#scroll-jadwal-sholat" },
        { id: "scroll-ke-jadwal-sholat-2", target: "#scroll-jadwal-sholat" },
        { id: "scroll-ke-jadwal-sholat-3", target: "#scroll-jadwal-sholat" },
        { id: "scroll-ke-galery", target: "#scroll-galery" },
        { id: "scroll-ke-galery-2", target: "#scroll-galery" },
        { id: "scroll-ke-galery-3", target: "#scroll-galery" },
        { id: "scroll-ke-footer", target: "#scroll-footer" },
        { id: "scroll-ke-footer-2", target: "#scroll-footer" },
        { id: "scroll-ke-hari-ini", target: "#jadwal-hari-ini" },
        { id: "scroll-ke-minggu-ini", target: "#jadwal-minggu-ini" },
        { id: "scroll-ke-minggu-depan", target: "#jadwal-minggu-depan" },
        {
            id: "scroll-ke-sudah-terlaksana",
            target: "#jadwal-sudah-terlaksana",
        },
        {
            id: "scroll-ke-sudah-terlaksana-2",
            target: "#jadwal-sudah-terlaksana",
        },
        { id: "scroll-ke-map", target: "#scroll-map" },
        { id: "scroll-ke-map-2", target: "#scroll-map" },
    ];

    scrollLinks.forEach((linkInfo) => {
        const scrollLink = document.getElementById(linkInfo.id);
        const targetId = linkInfo.target;
        const targetElement = document.querySelector(targetId);

        if (scrollLink && targetElement) {
            scrollLink.addEventListener("click", function (event) {
                event.preventDefault();

                targetElement.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            });
        }
    });
});

