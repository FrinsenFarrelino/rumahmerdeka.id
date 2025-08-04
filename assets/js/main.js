document.addEventListener('DOMContentLoaded', function () {
    // Initialize Lucide Icons
    lucide.createIcons();

    // Set current year in footer
    const yearSpan = document.getElementById('year');
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }

    // Countdown Timer Logic
    const countdownElement = document.getElementById('countdown');
    if (countdownElement) {
        const countdown = () => {
            const countDate = new Date('August 4, 2025 16:00:00').getTime();
            const now = new Date().getTime();
            const gap = countDate - now;

            if (gap < 0) {
                if (countdownElement.innerHTML.includes('PROMO')) return;
                countdownElement.innerHTML = '<div class="col-span-4 text-center text-2xl font-bold">PROMO TELAH BERAKHIR</div>';
                if(countdownInterval) clearInterval(countdownInterval);
                return;
            }

            const second = 1000, minute = second * 60, hour = minute * 60, day = hour * 24;
            const textDay = String(Math.floor(gap / day)).padStart(2, '0');
            const textHour = String(Math.floor((gap % day) / hour)).padStart(2, '0');
            const textMinute = String(Math.floor((gap % hour) / minute)).padStart(2, '0');
            const textSecond = String(Math.floor((gap % minute) / second)).padStart(2, '0');

            document.getElementById('days').innerText = textDay;
            document.getElementById('hours').innerText = textHour;
            document.getElementById('minutes').innerText = textMinute;
            document.getElementById('seconds').innerText = textSecond;
        };
        countdown();
        const countdownInterval = setInterval(countdown, 1000);
    }

    // --- Form Section Logic ---
    const showFormBtns = document.querySelectorAll('.show-form-trigger');
    const registrationSection = document.getElementById('pendaftaran');

    if (showFormBtns.length > 0 && registrationSection) {
        showFormBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                registrationSection.classList.remove('hidden');
                setTimeout(() => {
                    registrationSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            });
        });
    }

    // Form elements
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const formMessage = document.getElementById('formMessage');

    // --- Marital, Spouse, and Income Logic ---
    const maritalStatusRadios = document.querySelectorAll('input[name="status_perkawinan"]');
    const spouseFields = document.getElementById('spouseFields');
    const spouseInputs = spouseFields.querySelectorAll('input, textarea');
    const incomeSingleDiv = document.getElementById('incomeSingle');
    const incomeMarriedDiv = document.getElementById('incomeMarried');
    const incomeSingleRadios = document.querySelectorAll('input[name="penghasilan_sesuai"]');
    const incomeMarriedRadios = document.querySelectorAll('input[name="penghasilan_sesuai_gabungan"]');

    maritalStatusRadios.forEach(radio => {
        radio.addEventListener('change', (e) => {
            const isMarried = e.target.value === 'menikah';
            spouseFields.classList.toggle('hidden', !isMarried);
            spouseInputs.forEach(input => {
                input.required = isMarried;
            });
            incomeSingleDiv.classList.toggle('hidden', isMarried);
            incomeMarriedDiv.classList.toggle('hidden', !isMarried);
            if (isMarried) {
                incomeSingleRadios.forEach(r => { r.name = '_penghasilan_sesuai'; r.required = false; });
                incomeMarriedRadios.forEach(r => { r.name = 'penghasilan_sesuai'; r.required = true; });
            } else {
                incomeSingleRadios.forEach(r => { r.name = 'penghasilan_sesuai'; r.required = true; });
                incomeMarriedRadios.forEach(r => { r.name = '_penghasilan_sesuai_gabungan'; r.required = false; });
            }
        });
    });
    document.querySelector('input[name="status_perkawinan"]:checked').dispatchEvent(new Event('change'));

    // --- Success Modal Logic ---
    const successModal = document.getElementById('successModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    
    const showModal = () => {
        successModal.classList.remove('hidden');
        setTimeout(() => {
            successModal.classList.remove('opacity-0');
            successModal.querySelector('div').classList.remove('scale-95');
        }, 10);
    };

    const hideModal = () => {
        successModal.classList.add('opacity-0');
        successModal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            successModal.classList.add('hidden');
            if (registrationSection) {
                registrationSection.classList.add('hidden');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 300);
    };

    if(closeModalBtn) closeModalBtn.addEventListener('click', hideModal);
    if(successModal) successModal.addEventListener('click', (e) => {
        if (e.target === successModal) hideModal();
    });

    // --- Button Click Counter Logic ---
    const recordClick = (buttonId) => {
        const formData = new FormData();
        formData.append('button_id', buttonId);

        fetch('update_counter.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => { 
                    throw new Error(`Server Error: ${response.status} ${response.statusText} - ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                console.log(`Counter for '${buttonId}' updated successfully.`);
            } else {
                console.error(`Failed to update counter for '${buttonId}':`, data.message);
            }
        })
        .catch(error => {
            console.error('Counter Fetch Error:', error);
        });
    };

    document.getElementById('daftar-sekarang-btn')?.addEventListener('click', () => recordClick('daftar_sekarang_btn'));
    document.getElementById('saya-mau-daftar-btn')?.addEventListener('click', () => recordClick('saya_mau_daftar_btn'));
    
    // --- Form Submission Logic (AJAX) ---
    if(form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            
            recordClick('submitBtn');

            const originalBtnContent = submitBtn.innerHTML;
            submitBtn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengirim...`;
            submitBtn.disabled = true;
            formMessage.classList.add('hidden');

            const formData = new FormData(form);

            fetch('daftar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    showModal();
                    form.reset();
                    document.querySelector('input[name="status_perkawinan"]:checked').dispatchEvent(new Event('change'));
                } else {
                    formMessage.className = 'p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-100';
                    formMessage.textContent = 'Peringatan: ' + data.message + ' Mohon periksa kembali data Anda.';
                    formMessage.classList.remove('hidden');
                    formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            })
            .catch(error => {
                formMessage.className = 'p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100';
                formMessage.textContent = 'Koneksi internet bermasalah. Mohon periksa kembali dan coba lagi.';
                formMessage.classList.remove('hidden');
                console.error('Fetch Error:', error);
            })
            .finally(() => {
                submitBtn.innerHTML = originalBtnContent;
                submitBtn.disabled = false;
            });
        });
    }

    // --- Testimonial Slider Logic (Endless Loop with Arrows) ---
    const slider = document.getElementById('testimonial-slider');
    if (slider) {
        const track = document.getElementById('slider-track');
        const slides = Array.from(track.children);
        const nextBtn = document.getElementById('next-slide');
        const prevBtn = document.getElementById('prev-slide');
        
        if (slides.length > 1) {
            let currentSlideIndex = 0;
            let slideInterval;
            const slideDuration = 5000; // 5 detik

            const updateSlidePosition = () => {
                track.style.transform = `translateX(-${currentSlideIndex * 100}%)`;
            };
            
            const showSlide = (index) => {
                // The modulo operator creates the endless loop effect
                currentSlideIndex = (index + slides.length) % slides.length;
                updateSlidePosition();
            };

            const next = () => {
                showSlide(currentSlideIndex + 1);
            };

            const prev = () => {
                showSlide(currentSlideIndex - 1);
            };

            const startSlideShow = () => {
                clearInterval(slideInterval);
                slideInterval = setInterval(next, slideDuration);
            };

            nextBtn.addEventListener('click', () => {
                next();
                startSlideShow(); // Restart interval on manual click
            });

            prevBtn.addEventListener('click', () => {
                prev();
                startSlideShow(); // Restart interval on manual click
            });

            // Initialize slider
            startSlideShow();
        } else {
            // If only one slide, hide navigation
            if(nextBtn) nextBtn.style.display = 'none';
            if(prevBtn) prevBtn.style.display = 'none';
        }
    }
});
