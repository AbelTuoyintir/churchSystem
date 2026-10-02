<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Grace Community Church | Church Management</title>
  <!-- Tailwind CSS (CDN) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <!-- Google Fonts: Inter + Playfair Display -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
  <!-- Tailwind Config for custom fonts & colors -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'sans-serif'],
            display: ['Playfair Display', 'serif'],
          },
          colors: {
            brand: {
              dark: '#1e2f2a',
              DEFAULT: '#2b5e4a',
              light: '#3e7a63',
              pale: '#d9e9e1',
              cream: '#f5e7d9',
              muted: '#5b6d64',
            }
          }
        }
      }
    }
  </script>
</head>
<body class="font-sans bg-[#faf9f7] text-[#1e1e2a] leading-relaxed">

  <div class="max-w-7xl mx-auto px-5 sm:px-6 lg:px-8">

    <!-- ================= HEADER ================= -->
    <header class="py-5">
      <nav class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <!-- Logo -->
        <a href="#" class="flex items-center gap-3 shrink-0">
          <div class="w-11 h-11 rounded-xl bg-brand flex items-center justify-center text-brand-cream text-xl">
            <i class="fas fa-church"></i>
          </div>
          <span class="font-display font-bold text-2xl tracking-tight text-brand-dark">
            Grace<span class="text-brand-light font-semibold">Manage</span>
          </span>
        </a>

        <!-- Nav Links -->
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-[0.95rem] font-medium text-[#2e3a35]">
          <a href="#" class="hover:text-brand transition-colors">Home</a>
          <a href="#" class="hover:text-brand transition-colors">Features</a>
          <a href="#" class="hover:text-brand transition-colors">Community</a>
          <a href="#" class="hover:text-brand transition-colors">Resources</a>
          <a href="#" class="hover:text-brand transition-colors">Contact</a>
          <a href="#" class="inline-block bg-brand text-white font-semibold text-sm px-5 py-2 rounded-full shadow-md shadow-brand/25 hover:bg-[#1f4738] hover:-translate-y-px transition-all">
            Sign in
          </a>
        </div>
      </nav>
    </header>

    <!-- ================= HERO ================= -->
    <section class="grid grid-cols-1 lg:grid-cols-2 items-center gap-12 lg:gap-16 mt-10 mb-20">
      <!-- Hero Content -->
      <div>
        <h1 class="font-display font-bold text-4xl sm:text-5xl lg:text-[3.6rem] leading-[1.15] tracking-tight text-[#16231f] mb-6">
          Manage your
          <span class="bg-gradient-to-r from-brand-pale via-brand-pale/70 to-transparent px-2">church</span>
          with grace & efficiency
        </h1>
        <p class="text-lg sm:text-xl text-[#40514b] mb-8 max-w-[90%]">
          All-in-one platform for congregations, events, giving, and volunteers.
          Built for community — so you can focus on ministry, not admin.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap gap-4 mb-10">
          <a href="#" class="inline-flex items-center gap-2 bg-brand text-white font-semibold px-8 py-3.5 rounded-full shadow-lg shadow-brand/25 hover:bg-[#1f4738] hover:-translate-y-px transition-all">
            <i class="fas fa-church"></i> Start free trial
          </a>
          <a href="#" class="inline-flex items-center gap-2 border-[1.5px] border-brand text-brand font-semibold px-8 py-3.5 rounded-full hover:bg-brand/5 transition-all">
            <i class="fas fa-play-circle"></i> Watch demo
          </a>
        </div>

        <!-- Stats -->
        <div class="flex flex-wrap gap-10">
          <div>
            <h3 class="font-display font-bold text-2xl text-brand-dark">2.4k+</h3>
            <p class="text-brand-muted text-sm tracking-wide">Churches served</p>
          </div>
          <div>
            <h3 class="font-display font-bold text-2xl text-brand-dark">98%</h3>
            <p class="text-brand-muted text-sm tracking-wide">Member satisfaction</p>
          </div>
          <div>
            <h3 class="font-display font-bold text-2xl text-brand-dark">24/7</h3>
            <p class="text-brand-muted text-sm tracking-wide">Support</p>
          </div>
        </div>
      </div>

      <!-- Hero Image / Illustration -->
      <div class="relative bg-brand-pale rounded-[2.5rem] h-[300px] sm:h-[360px] lg:h-[380px] shadow-2xl shadow-brand/25 overflow-hidden
                  bg-[url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 400 300%22 width=%22100%25%22 height=%22100%25%22><path d=%22M0 200 Q 100 120 200 160 T 400 140 L 400 300 L 0 300 Z%22 fill=%22%23aacbbd%22 opacity=%220.3%22/><circle cx=%22120%22 cy=%22100%22 r=%2240%22 fill=%22%23f5e7d9%22 opacity=%220.5%22/><circle cx=%22300%22 cy=%2270%22 r=%2260%22 fill=%22%23c6ddd2%22 opacity=%220.4%22/><path d=%22M180 220 L200 180 L220 220 L200 240 Z%22 fill=%22%232b5e4a%22 opacity=%220.2%22/></svg>')] bg-cover bg-center">
        <span class="absolute bottom-6 right-8 text-7xl opacity-40 text-brand select-none">⛪</span>
      </div>
    </section>

    <!-- ================= FEATURE BADGES STRIP ================= -->
    <div class="flex flex-wrap justify-center gap-x-10 gap-y-5 bg-white/80 backdrop-blur-sm px-8 py-7 rounded-full shadow-xl shadow-black/5 border border-[#eae9e4] mb-20">
      <div class="flex items-center gap-3 font-medium text-[#2d4139]">
        <i class="fas fa-users text-2xl text-brand-light"></i>
        <span>Member directory</span>
      </div>
      <div class="flex items-center gap-3 font-medium text-[#2d4139]">
        <i class="fas fa-hand-holding-heart text-2xl text-brand-light"></i>
        <span>Online giving</span>
      </div>
      <div class="flex items-center gap-3 font-medium text-[#2d4139]">
        <i class="fas fa-calendar-check text-2xl text-brand-light"></i>
        <span>Event scheduling</span>
      </div>
      <div class="flex items-center gap-3 font-medium text-[#2d4139]">
        <i class="fas fa-envelope-open-text text-2xl text-brand-light"></i>
        <span>Communication</span>
      </div>
      <div class="flex items-center gap-3 font-medium text-[#2d4139]">
        <i class="fas fa-chart-line text-2xl text-brand-light"></i>
        <span>Reports & insights</span>
      </div>
    </div>

    <!-- ================= FEATURES GRID ================= -->
    <section class="pb-20">
      <h2 class="font-display font-bold text-3xl sm:text-4xl tracking-tight text-[#16231f] mb-4">
        Everything your church needs
      </h2>
      <p class="text-brand-muted text-lg max-w-2xl mb-12">
        From volunteer coordination to secure donations — simplify every aspect of church life.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
        <!-- Card 1 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#f0ede8] hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-brand/15 hover:border-brand-pale transition-all duration-300">
          <div class="w-14 h-14 rounded-2xl bg-brand-pale flex items-center justify-center text-2xl text-brand mb-6">
            <i class="fas fa-people-group"></i>
          </div>
          <h3 class="font-semibold text-xl text-[#1c2b25] mb-3">Member & volunteer care</h3>
          <p class="text-[#53635b] text-[0.98rem] leading-relaxed">
            Track attendance, groups, and volunteer schedules. Keep everyone connected and engaged.
          </p>
        </div>
        <!-- Card 2 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#f0ede8] hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-brand/15 hover:border-brand-pale transition-all duration-300">
          <div class="w-14 h-14 rounded-2xl bg-brand-pale flex items-center justify-center text-2xl text-brand mb-6">
            <i class="fas fa-circle-dollar-to-slot"></i>
          </div>
          <h3 class="font-semibold text-xl text-[#1c2b25] mb-3">Giving & donations</h3>
          <p class="text-[#53635b] text-[0.98rem] leading-relaxed">
            Secure online giving, recurring donations, and instant tax statements. Simplify generosity.
          </p>
        </div>
        <!-- Card 3 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#f0ede8] hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-brand/15 hover:border-brand-pale transition-all duration-300">
          <div class="w-14 h-14 rounded-2xl bg-brand-pale flex items-center justify-center text-2xl text-brand mb-6">
            <i class="fas fa-calendar-plus"></i>
          </div>
          <h3 class="font-semibold text-xl text-[#1c2b25] mb-3">Events & calendars</h3>
          <p class="text-[#53635b] text-[0.98rem] leading-relaxed">
            Plan services, small groups, and outreach. Shared calendars keep everyone in sync.
          </p>
        </div>
        <!-- Card 4 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#f0ede8] hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-brand/15 hover:border-brand-pale transition-all duration-300">
          <div class="w-14 h-14 rounded-2xl bg-brand-pale flex items-center justify-center text-2xl text-brand mb-6">
            <i class="fas fa-message"></i>
          </div>
          <h3 class="font-semibold text-xl text-[#1c2b25] mb-3">Messaging & updates</h3>
          <p class="text-[#53635b] text-[0.98rem] leading-relaxed">
            Send announcements via email, SMS, or in-app. Keep the congregation informed.
          </p>
        </div>
        <!-- Card 5 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#f0ede8] hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-brand/15 hover:border-brand-pale transition-all duration-300">
          <div class="w-14 h-14 rounded-2xl bg-brand-pale flex items-center justify-center text-2xl text-brand mb-6">
            <i class="fas fa-chart-pie"></i>
          </div>
          <h3 class="font-semibold text-xl text-[#1c2b25] mb-3">Insights & reports</h3>
          <p class="text-[#53635b] text-[0.98rem] leading-relaxed">
            Understand engagement, giving trends, and growth with easy-to-read dashboards.
          </p>
        </div>
        <!-- Card 6 -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-[#f0ede8] hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-brand/15 hover:border-brand-pale transition-all duration-300">
          <div class="w-14 h-14 rounded-2xl bg-brand-pale flex items-center justify-center text-2xl text-brand mb-6">
            <i class="fas fa-mobile-screen-button"></i>
          </div>
          <h3 class="font-semibold text-xl text-[#1c2b25] mb-3">Mobile friendly</h3>
          <p class="text-[#53635b] text-[0.98rem] leading-relaxed">
            Access everything from any device. Members can give, register, and connect on the go.
          </p>
        </div>
      </div>
    </section>

    <!-- ================= CTA BANNER ================= -->
    <section class="bg-brand-dark rounded-[3rem] px-8 sm:px-12 py-14 mb-20 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10
                    bg-[radial-gradient(circle_at_20%_30%,#2b5e4a_0%,#1e2f2a_70%)] text-white">
      <div>
        <h2 class="font-display font-bold text-3xl sm:text-4xl tracking-tight mb-3">
          Ready to serve better?
        </h2>
        <p class="text-[#c2d6cd] text-lg max-w-xl">
          Join thousands of churches using GraceManage to build stronger communities.
        </p>
      </div>
      <div class="flex flex-wrap gap-4 shrink-0">
        <a href="#" class="inline-flex items-center gap-2 bg-brand-cream text-brand-dark font-bold px-8 py-3.5 rounded-full shadow-lg shadow-black/30 hover:bg-[#fcf3ea] hover:-translate-y-px transition-all">
          Get started free
        </a>
        <a href="#" class="inline-flex items-center gap-2 border-[1.5px] border-[#b6d0c4] text-[#f0f7f3] font-semibold px-8 py-3.5 rounded-full hover:bg-white/10 hover:border-[#d0e6db] transition-all">
          Talk to sales
        </a>
      </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer class="border-t border-[#dfe6e1] pt-12 pb-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
        <!-- Brand Column -->
        <div>
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-brand flex items-center justify-center text-brand-cream text-lg">
              <i class="fas fa-church"></i>
            </div>
            <span class="font-display font-bold text-xl tracking-tight text-brand-dark">
              Grace<span class="text-brand-light font-semibold">Manage</span>
            </span>
          </div>
          <p class="text-brand-muted text-[0.95rem] max-w-xs mb-5">
            Empowering churches to thrive through simple, powerful management tools.
          </p>
          <div class="flex gap-5 text-xl text-[#4f6b5e]">
            <a href="#" class="hover:text-brand transition-colors" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="hover:text-brand transition-colors" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" class="hover:text-brand transition-colors" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" class="hover:text-brand transition-colors" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
          </div>
        </div>

        <!-- Product Links -->
        <div>
          <h4 class="font-semibold text-lg text-brand-dark mb-5">Product</h4>
          <ul class="space-y-3">
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Features</a></li>
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Pricing</a></li>
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Security</a></li>
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Mobile app</a></li>
          </ul>
        </div>

        <!-- Resources Links -->
        <div>
          <h4 class="font-semibold text-lg text-brand-dark mb-5">Resources</h4>
          <ul class="space-y-3">
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Blog</a></li>
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Help center</a></li>
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Community</a></li>
            <li><a href="#" class="text-brand-muted text-[0.95rem] hover:text-brand transition-colors">Webinars</a></li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h4 class="font-semibold text-lg text-brand-dark mb-5">Stay connected</h4>
          <p class="text-brand-muted text-[0.95rem] mb-3">
            Get updates and inspiration in your inbox.
          </p>
          <form class="flex bg-[#eef3f0] rounded-full p-1.5">
            <input type="email" placeholder="Your email address" aria-label="Email"
                   class="flex-1 bg-transparent border-none px-4 py-2.5 text-sm outline-none placeholder:text-brand-muted/70">
            <button type="button"
                    class="bg-brand text-white font-medium text-sm px-6 py-2.5 rounded-full hover:bg-[#1f4738] transition-colors">
              Subscribe
            </button>
          </form>
        </div>
      </div>

      <!-- Copyright -->
      <div class="text-center text-[#7a8b82] text-sm pt-10 mt-8 border-t border-[#e2eae5]">
        &copy; 2025 GraceManage. All rights reserved. • Built with
        <i class="fas fa-heart text-[#aacbbd]"></i> for churches.
      </div>
    </footer>
  </div>
</body>
</html>