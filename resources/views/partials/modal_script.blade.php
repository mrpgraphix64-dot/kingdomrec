
<!-- Modal Backdrop & Container -->
<div id="infoModal" class="fixed inset-0 z-[100] hidden overflow-y-auto font-sans" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop, centered flex container -->
    <div class="fixed inset-0 bg-slate-900/60 transition-opacity backdrop-blur-sm" onclick="window.closeModal()"></div>

    <!-- Modal Panel Container -->
    <div class="flex min-h-full items-center justify-center p-4 text-center">
        <!-- Main Card -->
        <div class="relative transform overflow-hidden rounded-[1.5rem] bg-white dark:bg-slate-800 text-left shadow-2xl transition-all w-full max-w-3xl border border-gray-100 dark:border-gray-700 my-4">
            
            <!-- Close Button -->
            <button type="button" onclick="window.closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors focus:outline-none z-10">
                <span class="material-symbols-outlined text-2xl font-light">close</span>
            </button>

            <!-- Scrollable Content - max-h set to prevent overflow on small screens, but aimed to fit -->
            <div class="max-h-[90vh] overflow-y-auto custom-scrollbar">
                
                <!-- Content Padding Wrapper -->
                <div class="p-6 md:p-8 pb-0">

                    <!-- Compact Header Section -->
                    <div class="flex items-start gap-5 mb-5">
                        <!-- Icon Circle -->
                        <div class="flex-shrink-0 inline-flex items-center justify-center w-12 h-12 rounded-full bg-kingdom-navy text-kingdom-gold shadow-lg shadow-kingdom-navy/20" id="modal-icon-container">
                            <span class="material-symbols-outlined text-2xl" id="modal-icon">campaign</span>
                        </div>
                        
                        <div class="flex-1 pt-1">
                            <!-- Breadcrumb/Subtitle -->
                            <p class="text-[0.65rem] font-bold text-kingdom-gold uppercase tracking-widest mb-1" id="modal-subtitle">SERVICES / ADVERTISING</p>
                            <!-- Main Title -->
                            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white leading-tight font-display tracking-tight" id="modal-title">Advertise Job</h2>
                        </div>
                    </div>

                    <!-- Dynamic Body Content -->
                    <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed text-sm md:text-base" id="modal-body">
                        <!-- JS Injected Content -->
                    </div>

                    <!-- Bottom Image Banner (Compact) -->
                    <div class="relative mt-5 mb-0 rounded-xl overflow-hidden h-28 md:h-32 group" id="modal-image-container">
                        <img id="modal-image" src="" alt="Banner" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent flex items-end p-5">
                            <p class="text-white italic text-sm md:text-base font-medium leading-relaxed" id="modal-quote">
                                "Connecting the right opportunity with the right talent starts with visibility."
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Footer Section (Compact) -->
                <div class="px-6 md:px-8 py-4 flex items-center justify-between mt-2 border-t border-gray-50 dark:border-gray-700/50">
                    <button onclick="window.closeModal()" class="text-xs md:text-sm font-bold text-slate-700 hover:text-slate-900 dark:text-gray-400 dark:hover:text-white transition-colors">
                        Close Window
                    </button>
                    
                    <a href="/contact" class="inline-flex items-center justify-center px-5 py-2.5 bg-kingdom-navy hover:bg-kingdom-gold hover:text-kingdom-navy text-white text-xs md:text-sm font-bold rounded-lg shadow-lg shadow-kingdom-navy/30 transition-all transform hover:-translate-y-0.5">
                        Contact Us Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    console.log('Modal script loaded');


    window.modalContent = {
        advertise: {
            subtitle: "SERVICES / ADVERTISING",
            title: "Advertise Job",
            icon: "campaign",
            body: `
                <p class="mb-4 text-sm md:text-base text-justify">Kingdom Recruitments is a UK-based job site that specializes in recruitment for the hospitality, leisure, and tourism industries. They offer a range of job opportunities across various sectors, including hotels, restaurants, bars, and event venues.</p>
                <p class="text-sm md:text-base text-justify">One of the reasons why job seekers might choose Kingdom Recruitments is that the site focuses on jobs within specific industries, which can make it easier for them to find relevant job openings. Additionally, Kingdom Recruitments offers a personalized approach to recruitment, with a team of experienced consultants who work closely with job seekers to match them with suitable roles. Another factor that might make Kingdom Recruitments stand out is their commitment to supporting their applicants throughout the recruitment process. They provide guidance and advice on CV writing, interview preparation, and salary negotiations, which can be helpful for job seekers who are new to the job market.</p>
            `,
            image: "https://images.unsplash.com/photo-1552664730-d307ca884978?q=80&w=2070&auto=format&fit=crop",
            quote: "\"Connecting the right opportunity with the right talent starts with visibility.\""
        },
        recruiter: {
            subtitle: "LEADERSHIP / VISION",
            title: "Recruiter Profiles",
            icon: "person_search",
            body: `
                <p class="mb-4 text-xs md:text-sm text-justify">As a Chairman & CEO of Kingdom Recruitments, Mr. Chowdhury has already transformed Kingdom Recruitments towards a high performing and a digital-centric organisation. Throughout his career, Mr. Chowdhury has a proven track record of leadership in transformation, change management and business development. Mr Chowdhury brings more than 15 years of experience, has strong background of study and work experience in Management, Marketing, HR, Administrative, International Business, Corporate & networking with virtuous presentation & Communication.</p>
                
                <p class="mb-4 text-xs md:text-sm text-justify">The CEO of Kingdom Recruitments had a deep understanding of business principles and practices, including financial management, marketing, sales, and strategic planning. He had a more then 3 years extensive knowledge of the recruitment industry, including employment laws, market trends, and best practices for sourcing and retaining top talent.</p>
                
                <p class="mb-4 text-xs md:text-sm text-justify">The CEO of Kingdom Recruitments have a highly communicating skills to build strong relationships with clients, applicants, and internal staff. He has a committed to providing exceptional customer service and building long-term relationships with clients. Mr Chowdhury be able to think strategically and make decisions that align with the agency's long-term goals.</p>

                <p class="mb-4 text-xs md:text-sm text-justify">The CEO has been well experienced to changing market conditions, technological advancements, and evolving client needs. Mr Chowdhury maintain highly ethical standards and maintain the trust of clients, applicants, and employees. He is a results-driven and able to motivate the team to achieve targets and exceed client expectations.</p>

                <p class="mb-4 text-xs md:text-sm text-justify">Mr Chowdhury is a great experienced professional which he attained having international visits in many countries who has brought few business to success. He Leading several successful businesses requires some sorts of inter-personal skills, extensive industry knowledge and the ability to develop and grow people in a team, which he is able to do as a mentor and as a business leader. Being a successful entrepreneur role model and cultures. Prior to joining as CEO, Mr Chowdhury served as a Founder & CEO of Digital Ride Ltd. It’s a leading ride-sharing industry in Bangladesh & registered in UK & USA.</p>

                <p class="text-xs md:text-sm text-justify">He is committed to continuous learning and staying up-to-date with industry trends and best practices. He has a Master of Business Administration (MBA) from University of Sunderland, UK and also attended several executive educational & professional programmes in UK. He has a (BA) Hons International Business at University of the West of Scotland & he studied Chartered Institute of Management Accountants (CIMA) from Wrexham Glyndwr University, Wales, UK. He is an innovative Entrepreneur, motivated to work in different areas and create new dimensions in the business world.</p>
            `,
            image: "https://images.unsplash.com/photo-1556761175-5973dc0f32e7?q=80&w=2664&auto=format&fit=crop", 
            quote: "\"Leadership is about empowering people to achieve their potential.\""
        },
        dream_job: {
            subtitle: "CAREERS / OPPORTUNITIES",
            title: "Find Your Dream Job",
            icon: "manage_search",
            body: `
                <p class="text-sm md:text-base text-justify">Finding your dream job can be a challenging but rewarding process. By utilizing Kingdom Recruitments and other job sites, identifying your career goals, using targeted keywords, creating a strong CV/resume, researching potential employers, networking, and being patient and persistent, you can increase your chances of finding the right job opportunity that aligns with your skills, interests, and values. Keep a positive attitude and stay motivated on your journey to finding your dream job.</p>
            `,
            image: "https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=2084&auto=format&fit=crop",
            quote: "\"Your career is a journey, and every step should move you closer to your passion.\""
        }
    };


    window.openModal = function(type) {
        console.log('openModal called with:', type);
        const modal = document.getElementById('infoModal');
        const content = window.modalContent[type];

        if (content) {
            // Update Text Content
            document.getElementById('modal-subtitle').textContent = content.subtitle;
            document.getElementById('modal-title').textContent = content.title;
            document.getElementById('modal-icon').textContent = content.icon;
            document.getElementById('modal-body').innerHTML = content.body;
            document.getElementById('modal-quote').textContent = content.quote;
            
            // Update Image
            document.getElementById('modal-image').src = content.image;

            // Show Modal
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            console.error('Content not found for type:', type);
        }
    };

    window.closeModal = function() {
        const modal = document.getElementById('infoModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    };

    // Close on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            window.closeModal();
        }
    });
</script>
