// // assets/js/main.js

// document.addEventListener('DOMContentLoaded', function() {
    
//     // Mobile Menu Toggle
//     const mobileToggle = document.querySelector('.mobile-menu-toggle');
//     const mainNav = document.querySelector('.main-nav');

//     if (mobileToggle) {
//         mobileToggle.addEventListener('click', function() {
//             mainNav.classList.toggle('active');
//             // Change icon
//             const icon = this.querySelector('i');
//             if (mainNav.classList.contains('active')) {
//                 icon.classList.remove('fa-bars');
//                 icon.classList.add('fa-times');
//             } else {
//                 icon.classList.remove('fa-times');
//                 icon.classList.add('fa-bars');
//             }
//         });
//     }

//     // Smooth scroll for anchor links (if any)
//     document.querySelectorAll('a[href^="#"]').forEach(anchor => {
//         anchor.addEventListener('click', function (e) {
//             e.preventDefault();
//             const target = document.querySelector(this.getAttribute('href'));
//             if (target) {
//                 target.scrollIntoView({
//                     behavior: 'smooth'
//                 });
//             }
//         });
//     });

// });


// assets/js/main.js

document.addEventListener('DOMContentLoaded', function() {

    // --- Mobile Menu Toggle (Hamburger) ---
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            const icon = this.querySelector('i');
            if (mainNav.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // --- Mobile Dropdown Toggle (Accordion) ---
    const dropdownParents = document.querySelectorAll('.has-dropdown');

    dropdownParents.forEach(parent => {
        const link = parent.querySelector('a');

        link.addEventListener('click', function(e) {
            // Only on mobile (screen width < 992px)
            if (window.innerWidth < 992) {
                e.preventDefault(); // Prevent going to link
                parent.classList.toggle('active-dropdown');
            }
        });
    });
    
    
     /* --- Enrol Now Modal --- */
    const enrollModal = document.getElementById('enrollModal');
    const openEnrollBtn = document.getElementById('openEnrollModal');
    const closeEnrollBtn = document.getElementById('closeEnrollModal');

    if (enrollModal && openEnrollBtn) {
        // Open modal
        openEnrollBtn.addEventListener('click', function() {
            enrollModal.classList.add('active');
            document.body.classList.add('modal-open');
        });

        // Close modal
        if (closeEnrollBtn) {
            closeEnrollBtn.addEventListener('click', function() {
                enrollModal.classList.remove('active');
                document.body.classList.remove('modal-open');
            });
        }

        // Close on overlay click
        enrollModal.addEventListener('click', function(e) {
            if (e.target === enrollModal) {
                enrollModal.classList.remove('active');
                document.body.classList.remove('modal-open');
            }
        });

        // Close on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && enrollModal.classList.contains('active')) {
                enrollModal.classList.remove('active');
                document.body.classList.remove('modal-open');
            }
        });
    }

  /* --- Generic Modal Handler (works for all Enrol modals) --- */
    function setupModal(modalId, openBtnId, closeBtnId) {
        const modal = document.getElementById(modalId);
        const openBtn = document.getElementById(openBtnId);
        const closeBtn = document.getElementById(closeBtnId);

        if (!modal || !openBtn) return;

        openBtn.addEventListener('click', function() {
            modal.classList.add('active');
            document.body.classList.add('modal-open');
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.classList.remove('active');
                document.body.classList.remove('modal-open');
            });
        }

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.remove('active');
                document.body.classList.remove('modal-open');
            }
        });
    }

    // Initialize modals
      setupModal('enrollModal', 'openEnrollModal', 'closeEnrollModal');          // Logistics
    setupModal('capacityModal', 'openCapacityModal', 'closeCapacityModal');    // Capacity Building
    setupModal('happinessModal', 'openHappinessModal', 'closeHappinessModal'); // Happiness

    // Close any open modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.enroll-modal-overlay.active').forEach(function(modal) {
                modal.classList.remove('active');
                document.body.classList.remove('modal-open');
            });
        }
    });

});

    // --- About Page Tab Switching ---
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabPanes = document.querySelectorAll('.tab-pane');

    if (tabButtons.length > 0) {
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and panes
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');

                // Show the corresponding tab pane
                const targetId = this.getAttribute('data-target');
                const targetPane = document.getElementById(targetId);
                if (targetPane) {
                    targetPane.classList.add('active');
                }
            });
        });
    }
    
     /* --- Header Scroll Effect --- */
    const siteHeader = document.getElementById('siteHeader');
    if (siteHeader) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }
        });
    }