document.addEventListener("DOMContentLoaded", function () {

    /* =====================================================
       MINIMUM APPOINTMENT DATE
    ===================================================== */

    const appointmentDate =
        document.querySelector('input[name="appointment_date"]');

    if (appointmentDate) {

        const today = new Date();

        const localToday =
            new Date(
                today.getTime() -
                today.getTimezoneOffset() * 60000
            )
            .toISOString()
            .split("T")[0];

        appointmentDate.min = localToday;
    }


    /* =====================================================
       SCROLL REVEAL
    ===================================================== */

    const revealObserver =
        new IntersectionObserver(
            function (entries, observer) {

                entries.forEach(function (entry) {

                    if (entry.isIntersecting) {

                        entry.target.classList.add(
                            "is-visible"
                        );

                        observer.unobserve(
                            entry.target
                        );
                    }

                });

            },
            {
                threshold: 0.12
            }
        );


    document
        .querySelectorAll(".reveal")
        .forEach(function (element) {

            revealObserver.observe(element);

        });


    /* =====================================================
       AFFORDABILITY CAMPAIGN ROTATION
    ===================================================== */

    const campaignLines =
        document.querySelectorAll(".campaign-line");

    if (campaignLines.length > 1) {

        let currentLine = 0;

        setInterval(function () {

            campaignLines[currentLine]
                .classList
                .remove("active");

            currentLine =
                (currentLine + 1) %
                campaignLines.length;

            campaignLines[currentLine]
                .classList
                .add("active");

        }, 2600);

    }


    /* =====================================================
       STAT COUNTERS
    ===================================================== */

    const statObserver =
        new IntersectionObserver(
            function (entries, observer) {

                entries.forEach(function (entry) {

                    if (!entry.isIntersecting) {
                        return;
                    }

                    const element = entry.target;

                    const target =
                        Number(
                            element.dataset.count
                        );

                    const suffix =
                        element.textContent.includes("+")
                            ? "+"
                            : "";

                    const startTime =
                        performance.now();

                    function animateCounter(time) {

                        const progress =
                            Math.min(
                                (time - startTime) /
                                1200,
                                1
                            );

                        const easedProgress =
                            1 -
                            Math.pow(
                                1 - progress,
                                3
                            );

                        element.textContent =
                            Math.floor(
                                target *
                                easedProgress
                            )
                            .toLocaleString("en-IN") +
                            suffix;

                        if (progress < 1) {

                            requestAnimationFrame(
                                animateCounter
                            );

                        }

                    }

                    requestAnimationFrame(
                        animateCounter
                    );

                    observer.unobserve(element);

                });

            },
            {
                threshold: 0.6
            }
        );


    document
        .querySelectorAll("[data-count]")
        .forEach(function (element) {

            statObserver.observe(element);

        });


    /* =====================================================
       SMOOTH INTERNAL LINKS
    ===================================================== */

    document
        .querySelectorAll('a[href^="#"]')
        .forEach(function (link) {

            link.addEventListener(
                "click",
                function (event) {

                    const id =
                        link.getAttribute("href");

                    const target =
                        id && id !== "#"
                            ? document.querySelector(id)
                            : null;

                    if (!target) {
                        return;
                    }

                    event.preventDefault();

                    target.scrollIntoView({
                        behavior: "smooth",
                        block: "start"
                    });

                }
            );

        });


    /* =====================================================
       PHONE NUMBER VALIDATION
    ===================================================== */

    document
        .querySelectorAll('input[type="tel"]')
        .forEach(function (input) {

            input.addEventListener(
                "input",
                function () {

                    input.value =
                        input.value
                            .replace(/\D/g, "")
                            .slice(0, 10);

                }
            );

        });


    /* =====================================================
       FORM SUBMIT PROTECTION
    ===================================================== */

    document
        .querySelectorAll("form")
        .forEach(function (form) {

            form.addEventListener(
                "submit",
                function () {

                    const button =
                        form.querySelector(
                            'button[type="submit"]'
                        );

                    if (!button) {
                        return;
                    }

                    button.disabled = true;

                    button.innerHTML =
                        '<i class="fas fa-spinner fa-spin"></i> Sending...';

                }
            );

        });

});

// =============================================================

/* =========================================================
   COMPACT SPECIALITY STACKING
   ========================================================= */

function createCompactSpecialityStacks1() {

    const grid1 =
        document.querySelector('.speciality-grid');

    if (!grid1) {
        return;
    }


    /* Prevent duplicate initialization */

    if (
        grid1.dataset.compactStack1 === 'true'
    ) {
        return;
    }


    /* Get only original cards */

    const cards1 =
        Array.from(
            grid1.children
        ).filter(
            function(element1) {

                return element1.classList.contains(
                    'speciality-card'
                );

            }
        );


    if (!cards1.length) {
        return;
    }


    grid1.dataset.compactStack1 =
        'true';


    /* Determine columns */

    let columnCount1 = 4;


    if (window.innerWidth <= 1100) {

        columnCount1 = 2;

    }


    if (window.innerWidth <= 650) {

        columnCount1 = 1;

    }


    /* Create columns */

    const columns1 = [];


    for (
        let index1 = 0;
        index1 < columnCount1;
        index1++
    ) {

        const column1 =
            document.createElement('div');

        column1.className =
            'speciality-stack-column1';

        columns1.push(column1);

        grid1.appendChild(column1);

    }


    /* Distribute existing cards */

    cards1.forEach(
        function(card1, index1) {

            const columnIndex1 =
                index1 % columnCount1;

            columns1[columnIndex1].appendChild(
                card1
            );

        }
    );


    /* Stack order */

    columns1.forEach(
        function(column1) {

            const stackCards1 =
                column1.querySelectorAll(
                    '.speciality-card'
                );


            stackCards1.forEach(
                function(card1, index1) {

                    card1.style.zIndex =
                        index1 + 1;

                }
            );

        }
    );

}


/* =========================================================
   START
   ========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function() {

        createCompactSpecialityStacks1();

    }
);

// =================================================================