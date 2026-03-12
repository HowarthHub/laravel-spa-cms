<?php

namespace Database\Seeders;

use App\Models\CategoryModel;
use App\Models\ContactEnquiryModel;
use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use App\Models\MenuItemModel;
use App\Models\MenuModel;
use App\Models\PageModel;
use App\Models\PostModel;
use App\Models\ServiceModel;
use App\Models\TagModel;
use App\Models\UserModel;
use Illuminate\Database\Seeder;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = UserModel::first();
        $authorId = $admin->id;

        // ── Users ──────────────────────────────────────────
        $editor = UserModel::create([
            'name' => 'Sarah Mitchell',
            'email' => 'sarah@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'last_login_at' => now()->subDays(2),
        ]);
        $editor->assignRole('editor');

        $viewer = UserModel::create([
            'name' => 'James Cooper',
            'email' => 'james@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'last_login_at' => now()->subWeek(),
        ]);
        $viewer->assignRole('viewer');

        $inactive = UserModel::create([
            'name' => 'Emily Watson',
            'email' => 'emily@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
            'is_active' => false,
        ]);
        $inactive->assignRole('editor');

        // ── Categories ─────────────────────────────────────
        $categories = [];
        $catData = [
            ['name' => 'News', 'slug' => 'news', 'description' => 'Company news and announcements'],
            ['name' => 'Tutorials', 'slug' => 'tutorials', 'description' => 'How-to guides and walkthroughs'],
            ['name' => 'Industry Insights', 'slug' => 'industry-insights', 'description' => 'Thought leadership and trends'],
            ['name' => 'Case Studies', 'slug' => 'case-studies', 'description' => 'Client success stories'],
            ['name' => 'Product Updates', 'slug' => 'product-updates', 'description' => 'New features and releases'],
        ];

        foreach ($catData as $i => $cat) {
            $categories[] = CategoryModel::create(array_merge($cat, ['sort_order' => $i]));
        }

        // ── Tags ───────────────────────────────────────────
        $tags = [];
        $tagData = ['Laravel', 'Vue.js', 'Design', 'Performance', 'Security', 'SEO', 'DevOps', 'Mobile'];

        foreach ($tagData as $name) {
            $tags[] = TagModel::create([
                'name' => $name,
                'slug' => str($name)->slug()->toString(),
            ]);
        }

        // ── Pages ──────────────────────────────────────────
        $pages = [
            [
                'title' => 'Home',
                'slug' => 'home',
                'content' => $this->tiptapContent('Welcome to our website. We help businesses grow through smart digital solutions.'),
                'excerpt' => 'Welcome to our website.',
                'template' => 'landing',
                'status' => 'published',
                'published_at' => now()->subMonths(3),
                'meta_title' => 'Home | My Site',
                'meta_description' => 'We help businesses grow through smart digital solutions.',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => $this->tiptapContent('Founded in 2020, we are a team of passionate developers and designers dedicated to delivering exceptional digital experiences for our clients across the UK.'),
                'excerpt' => 'Learn more about our team and mission.',
                'template' => 'default',
                'status' => 'published',
                'published_at' => now()->subMonths(3),
                'meta_title' => 'About Us | My Site',
                'meta_description' => 'Learn about our team, our values, and what drives us.',
            ],
            [
                'title' => 'Services',
                'slug' => 'services',
                'content' => $this->tiptapContent('We offer web development, design, SEO, and digital marketing services tailored to your business needs.'),
                'excerpt' => 'Explore our full range of services.',
                'template' => 'full-width',
                'status' => 'published',
                'published_at' => now()->subMonths(2),
                'meta_title' => 'Our Services',
                'meta_description' => 'Web development, design, SEO and digital marketing services.',
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => $this->tiptapContent('Get in touch with us today. We would love to hear about your project.'),
                'excerpt' => 'Reach out to discuss your project.',
                'template' => 'contact',
                'status' => 'published',
                'published_at' => now()->subMonths(2),
                'meta_title' => 'Contact Us',
                'meta_description' => 'Get in touch with our team to discuss your next project.',
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => $this->tiptapContent('This privacy policy sets out how we use and protect any information that you provide when using this website. We are committed to ensuring that your privacy is protected.'),
                'excerpt' => 'Our privacy policy.',
                'template' => 'default',
                'status' => 'published',
                'published_at' => now()->subMonths(1),
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-and-conditions',
                'content' => $this->tiptapContent('Please read these terms and conditions carefully before using our website or services.'),
                'excerpt' => 'Terms of use for our website.',
                'template' => 'default',
                'status' => 'published',
                'published_at' => now()->subMonths(1),
            ],
            [
                'title' => 'Careers',
                'slug' => 'careers',
                'content' => $this->tiptapContent('We are always looking for talented individuals to join our growing team.'),
                'excerpt' => 'Join our team.',
                'template' => 'default',
                'status' => 'draft',
            ],
        ];

        $pageModels = [];
        foreach ($pages as $page) {
            $pageModels[] = PageModel::create(array_merge($page, [
                'author_id' => $authorId,
            ]));
        }

        // Service sub-pages under Services
        $servicePages = [
            ['title' => 'Web Development', 'slug' => 'web-development', 'template' => 'default'],
            ['title' => 'Web Design', 'slug' => 'web-design', 'template' => 'default'],
            ['title' => 'SEO Services', 'slug' => 'seo-services', 'template' => 'default'],
        ];

        $servicesPage = $pageModels[2]; // Services page
        foreach ($servicePages as $sp) {
            PageModel::create([
                'title' => $sp['title'],
                'slug' => $sp['slug'],
                'content' => $this->tiptapContent("Learn more about our {$sp['title']} services. We deliver results-driven solutions tailored to your business."),
                'template' => $sp['template'],
                'status' => 'published',
                'published_at' => now()->subWeeks(3),
                'parent_id' => $servicesPage->id,
                'author_id' => $authorId,
                'meta_title' => "{$sp['title']} | My Site",
            ]);
        }

        // ── Posts ──────────────────────────────────────────
        $posts = [
            [
                'title' => 'Getting Started with Laravel 12',
                'slug' => 'getting-started-with-laravel-12',
                'content' => $this->tiptapContent('Laravel 12 brings exciting new features including improved performance, better developer experience, and powerful new tools for building modern web applications. In this post, we explore what\'s new and how to get started.'),
                'excerpt' => 'Explore the latest features in Laravel 12 and how to make the most of them.',
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'meta_title' => 'Getting Started with Laravel 12',
                'meta_description' => 'A comprehensive guide to the new features in Laravel 12.',
                'categories' => [1, 0],
                'tags' => [0],
            ],
            [
                'title' => 'Building Reactive UIs with Vue 3 Composition API',
                'slug' => 'building-reactive-uis-vue-3',
                'content' => $this->tiptapContent('The Composition API in Vue 3 provides a flexible and powerful way to organise component logic. We walk through real-world examples showing how to build clean, maintainable reactive interfaces.'),
                'excerpt' => 'Master the Vue 3 Composition API with practical examples.',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'categories' => [1],
                'tags' => [1, 2],
            ],
            [
                'title' => '10 Tips for Better Website Performance',
                'slug' => '10-tips-better-website-performance',
                'content' => $this->tiptapContent('Website speed directly impacts user experience and SEO rankings. Here are ten practical tips to optimise your site\'s load time, from image compression to lazy loading and caching strategies.'),
                'excerpt' => 'Practical tips to speed up your website and improve user experience.',
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'meta_title' => '10 Tips for Better Website Performance',
                'categories' => [2],
                'tags' => [3, 5],
            ],
            [
                'title' => 'How We Rebuilt Our Client Portal in 6 Weeks',
                'slug' => 'rebuilt-client-portal-6-weeks',
                'content' => $this->tiptapContent('When our client needed a complete portal rebuild, we delivered in just six weeks using Laravel and Vue. Here\'s how we planned, built, and launched on time.'),
                'excerpt' => 'A case study on rapid delivery for a client portal rebuild.',
                'status' => 'published',
                'published_at' => now()->subDays(12),
                'categories' => [3],
                'tags' => [0, 1],
            ],
            [
                'title' => 'The Importance of SSL and Web Security in 2026',
                'slug' => 'importance-ssl-web-security-2026',
                'content' => $this->tiptapContent('With cyber threats on the rise, website security has never been more important. We look at the essential security measures every website should have in place, from SSL certificates to content security policies.'),
                'excerpt' => 'Why web security matters more than ever and what you should be doing about it.',
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'categories' => [2],
                'tags' => [4],
            ],
            [
                'title' => 'Introducing Our New Dashboard Features',
                'slug' => 'new-dashboard-features',
                'content' => $this->tiptapContent('We have been busy building new features for our dashboard including dark mode, improved analytics, and a completely redesigned media library.'),
                'excerpt' => 'Check out the latest updates to our admin dashboard.',
                'status' => 'published',
                'published_at' => now()->subDays(20),
                'categories' => [4],
                'tags' => [2],
            ],
            [
                'title' => 'SEO Best Practices for Small Businesses',
                'slug' => 'seo-best-practices-small-businesses',
                'content' => $this->tiptapContent('Small businesses can compete with larger brands online by following proven SEO strategies. This guide covers everything from local SEO to content marketing.'),
                'excerpt' => 'A practical SEO guide for small business owners.',
                'status' => 'published',
                'published_at' => now()->subDays(25),
                'categories' => [2],
                'tags' => [5],
            ],
            [
                'title' => 'Deploying Laravel Apps with Zero Downtime',
                'slug' => 'deploying-laravel-zero-downtime',
                'content' => $this->tiptapContent('Zero-downtime deployments ensure your users never see an error page during updates. We cover how to set up automated deployments using Forge, Envoyer, and GitHub Actions.'),
                'excerpt' => 'Learn how to deploy your Laravel apps without any downtime.',
                'status' => 'published',
                'published_at' => now()->subDays(30),
                'categories' => [1],
                'tags' => [0, 6],
            ],
            [
                'title' => 'Mobile-First Design: Why It Matters',
                'slug' => 'mobile-first-design-why-it-matters',
                'content' => $this->tiptapContent('Over 60% of web traffic now comes from mobile devices. Designing mobile-first isn\'t just a trend — it\'s essential for reaching your audience effectively.'),
                'excerpt' => 'Why every website should be designed mobile-first.',
                'status' => 'scheduled',
                'published_at' => now()->addDays(3),
                'categories' => [2],
                'tags' => [2, 7],
            ],
            [
                'title' => 'Upcoming: Our Guide to Headless CMS Architecture',
                'slug' => 'guide-headless-cms-architecture',
                'content' => $this->tiptapContent('Draft: We are working on a comprehensive guide to headless CMS architecture and when it makes sense for your project.'),
                'excerpt' => 'Coming soon — headless CMS deep dive.',
                'status' => 'draft',
                'categories' => [1],
                'tags' => [0],
            ],
        ];

        foreach ($posts as $postData) {
            $catIndexes = $postData['categories'] ?? [];
            $tagIndexes = $postData['tags'] ?? [];
            unset($postData['categories'], $postData['tags']);

            $post = PostModel::create(array_merge($postData, [
                'author_id' => $authorId,
            ]));

            $catIds = collect($catIndexes)->map(fn ($i) => $categories[$i]->id)->all();
            $tagIds = collect($tagIndexes)->map(fn ($i) => $tags[$i]->id)->all();

            if ($catIds) $post->categories()->sync($catIds);
            if ($tagIds) $post->tags()->sync($tagIds);
        }

        // ── Enquiries ─────────────────────────────────────
        $enquiries = [
            ['name' => 'David Thompson', 'email' => 'david@acmecorp.co.uk', 'phone' => '07700 900123', 'subject' => 'Website redesign enquiry', 'message' => 'Hi, we are looking to redesign our company website. Could you provide a quote and timeline? We currently have about 30 pages and want a modern, mobile-friendly design.', 'status' => 'new'],
            ['name' => 'Rachel Green', 'email' => 'rachel@greendesigns.com', 'phone' => '07700 900456', 'subject' => 'SEO services', 'message' => 'We have been struggling with our search rankings and would like to discuss your SEO services. Our site gets about 5,000 visits a month but we want to grow significantly.', 'status' => 'new'],
            ['name' => 'Mark Williams', 'email' => 'mark@techsolutions.io', 'phone' => '07700 900789', 'subject' => 'Partnership opportunity', 'message' => 'I run a software consultancy and I think there could be a great partnership opportunity between our companies. Would love to arrange a call to discuss.', 'status' => 'new'],
            ['name' => 'Sophie Clarke', 'email' => 'sophie@bluewave.co.uk', 'subject' => 'E-commerce website', 'message' => 'We are a small retailer looking to launch an online shop. Could you let us know if this is something you offer and roughly what the costs would be?', 'status' => 'read', 'read_at' => now()->subDays(1)],
            ['name' => 'Tom Harrison', 'email' => 'tom@harrisonplumbing.co.uk', 'phone' => '07700 900321', 'subject' => 'Simple business website', 'message' => 'I need a basic 5-page website for my plumbing business. Just need a home page, about, services, gallery and contact page. What would this cost?', 'status' => 'replied', 'read_at' => now()->subDays(3), 'replied_at' => now()->subDays(2), 'reply_note' => 'Sent quote for 5-page brochure site. Follow up next week.'],
            ['name' => 'Lisa Brown', 'email' => 'lisa@brownbakery.com', 'subject' => 'Updating existing site', 'message' => 'Our current website is very outdated and we need to update the design and add an online ordering system. The site was built in WordPress about 5 years ago.', 'status' => 'replied', 'read_at' => now()->subDays(5), 'replied_at' => now()->subDays(4), 'reply_note' => 'Booked discovery call for Friday.'],
            ['name' => 'Chris Evans', 'email' => 'chris@evansarchitects.co.uk', 'phone' => '07700 900654', 'subject' => 'Portfolio website', 'message' => 'We need a portfolio website to showcase our architectural projects. We want something minimal and elegant with large images.', 'status' => 'archived', 'read_at' => now()->subWeeks(2), 'replied_at' => now()->subWeeks(2)],
            ['name' => 'Amanda Price', 'email' => 'amanda@pricelaw.co.uk', 'phone' => '07700 900987', 'subject' => 'Legal firm website', 'message' => 'Our law firm needs a professional website. We need it to look trustworthy and include sections for each of our practice areas. GDPR compliance is critical.', 'status' => 'new'],
        ];

        foreach ($enquiries as $i => $enq) {
            ContactEnquiryModel::create(array_merge($enq, [
                'ip_address' => '192.168.1.' . ($i + 10),
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => now()->subDays($i * 2),
            ]));
        }

        // ── Forms ──────────────────────────────────────────
        $contactForm = FormModel::create([
            'name' => 'Contact Form',
            'handle' => 'contact',
            'fields' => [
                ['label' => 'Full Name', 'type' => 'text', 'required' => true, 'placeholder' => 'Your full name'],
                ['label' => 'Email Address', 'type' => 'email', 'required' => true, 'placeholder' => 'you@example.com'],
                ['label' => 'Phone Number', 'type' => 'tel', 'required' => false, 'placeholder' => '07700 000000'],
                ['label' => 'Subject', 'type' => 'select', 'required' => true, 'options' => ['General Enquiry', 'Quote Request', 'Support', 'Partnership']],
                ['label' => 'Message', 'type' => 'textarea', 'required' => true, 'placeholder' => 'How can we help?'],
                ['label' => 'Subscribe to newsletter', 'type' => 'checkbox', 'required' => false],
            ],
            'success_message' => 'Thank you for your message. We will be in touch shortly.',
            'notification_email' => 'hello@example.com',
            'is_active' => true,
        ]);

        $quoteForm = FormModel::create([
            'name' => 'Request a Quote',
            'handle' => 'quote',
            'fields' => [
                ['label' => 'Name', 'type' => 'text', 'required' => true, 'placeholder' => 'Your name'],
                ['label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => 'you@example.com'],
                ['label' => 'Company', 'type' => 'text', 'required' => false, 'placeholder' => 'Company name'],
                ['label' => 'Budget', 'type' => 'select', 'required' => true, 'options' => ['Under £5,000', '£5,000 - £10,000', '£10,000 - £25,000', '£25,000+']],
                ['label' => 'Project Details', 'type' => 'textarea', 'required' => true, 'placeholder' => 'Tell us about your project'],
            ],
            'success_message' => 'Thanks! We will review your requirements and get back to you within 24 hours.',
            'notification_email' => 'quotes@example.com',
            'is_active' => true,
        ]);

        FormModel::create([
            'name' => 'Newsletter Signup',
            'handle' => 'newsletter',
            'fields' => [
                ['label' => 'Email Address', 'type' => 'email', 'required' => true, 'placeholder' => 'Enter your email'],
                ['label' => 'First Name', 'type' => 'text', 'required' => false, 'placeholder' => 'First name'],
            ],
            'success_message' => 'You have been subscribed to our newsletter.',
            'is_active' => true,
        ]);

        // Link contact form to Contact Us page
        $contactPage = PageModel::where('slug', 'contact-us')->first();
        if ($contactPage) {
            $contactPage->update(['form_id' => $contactForm->id]);
        }

        // Demo submissions for the contact form
        $demoSubmissions = [
            ['Full Name' => 'Alice Johnson', 'Email Address' => 'alice@example.com', 'Phone Number' => '07700 111222', 'Subject' => 'General Enquiry', 'Message' => 'Hi, I would love to know more about your services. Do you offer free consultations?', 'Subscribe to newsletter' => true],
            ['Full Name' => 'Ben Carter', 'Email Address' => 'ben@carter.co.uk', 'Subject' => 'Quote Request', 'Message' => 'We need a new website for our restaurant. About 8 pages with an online booking system.', 'Subscribe to newsletter' => false],
            ['Full Name' => 'Claire Davis', 'Email Address' => 'claire@davisdesign.com', 'Phone Number' => '07700 333444', 'Subject' => 'Partnership', 'Message' => 'I run a design studio and would be interested in partnering on projects.', 'Subscribe to newsletter' => true],
            ['Full Name' => 'Daniel Evans', 'Email Address' => 'dan@evansltd.co.uk', 'Phone Number' => '07700 555666', 'Subject' => 'Support', 'Message' => 'The contact form on our site seems to be broken. Can you take a look?', 'Subscribe to newsletter' => false],
            ['Full Name' => 'Emma Foster', 'Email Address' => 'emma@foster.io', 'Subject' => 'General Enquiry', 'Message' => 'Do you build mobile apps as well as websites?', 'Subscribe to newsletter' => true],
        ];

        foreach ($demoSubmissions as $i => $data) {
            FormSubmissionModel::create([
                'form_id' => $contactForm->id,
                'data' => $data,
                'ip_address' => '192.168.1.' . (100 + $i),
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                'created_at' => now()->subDays($i * 3),
            ]);
        }

        // ── Services ─────────────────────────────────────
        $services = [
            [
                'title' => 'Web Development',
                'slug' => 'web-development',
                'short_description' => 'Bespoke web applications built with modern frameworks and best practices.',
                'content' => $this->tiptapContent('We build fast, scalable web applications using Laravel, Vue.js, and other cutting-edge technologies. From simple brochure sites to complex platforms, we deliver clean code and exceptional user experiences.'),
                'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
                'features' => ['Custom Laravel applications', 'API development & integration', 'Database design & optimisation', 'Performance tuning', 'Ongoing maintenance & support'],
                'cta_text' => 'Start Your Project',
                'cta_link' => '/contact-us',
                'sort_order' => 0,
                'status' => 'published',
                'published_at' => now()->subMonths(2),
                'meta_title' => 'Web Development Services',
                'meta_description' => 'Bespoke web development using Laravel, Vue.js and modern frameworks.',
            ],
            [
                'title' => 'Web Design',
                'slug' => 'web-design',
                'short_description' => 'Beautiful, responsive designs that engage your audience and drive conversions.',
                'content' => $this->tiptapContent('Our design process puts your users first. We create stunning, mobile-responsive designs that not only look great but convert visitors into customers. Every design is crafted to reflect your brand identity.'),
                'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
                'features' => ['UI/UX design', 'Mobile-first responsive layouts', 'Brand identity & style guides', 'Wireframing & prototyping', 'Accessibility compliance'],
                'cta_text' => 'View Our Portfolio',
                'cta_link' => '/contact-us',
                'sort_order' => 1,
                'status' => 'published',
                'published_at' => now()->subMonths(2),
                'meta_title' => 'Web Design Services',
                'meta_description' => 'Beautiful, responsive web design that drives conversions.',
            ],
            [
                'title' => 'SEO & Digital Marketing',
                'slug' => 'seo-digital-marketing',
                'short_description' => 'Get found online with our proven search engine optimisation strategies.',
                'content' => $this->tiptapContent('We help businesses climb the search rankings with data-driven SEO strategies. From technical SEO audits to content marketing and link building, we deliver measurable results that grow your organic traffic.'),
                'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
                'features' => ['Technical SEO audits', 'Keyword research & strategy', 'Content marketing', 'Local SEO optimisation', 'Monthly reporting & analytics'],
                'cta_text' => 'Improve Your Rankings',
                'cta_link' => '/contact-us',
                'sort_order' => 2,
                'status' => 'published',
                'published_at' => now()->subMonths(2),
                'meta_title' => 'SEO & Digital Marketing Services',
                'meta_description' => 'Data-driven SEO and digital marketing to grow your online presence.',
            ],
            [
                'title' => 'E-commerce Solutions',
                'slug' => 'ecommerce-solutions',
                'short_description' => 'Powerful online shops that make selling easy and secure.',
                'content' => $this->tiptapContent('Whether you are launching your first online shop or scaling an existing one, we build e-commerce platforms that are fast, secure, and easy to manage. We integrate with all major payment providers and shipping services.'),
                'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
                'features' => ['Custom e-commerce platforms', 'Payment gateway integration', 'Inventory management', 'Order tracking & fulfilment', 'Security & PCI compliance'],
                'cta_text' => 'Launch Your Shop',
                'cta_link' => '/contact-us',
                'sort_order' => 3,
                'status' => 'published',
                'published_at' => now()->subWeeks(3),
                'meta_title' => 'E-commerce Development Services',
                'meta_description' => 'Custom e-commerce solutions for businesses of all sizes.',
            ],
            [
                'title' => 'Hosting & Support',
                'slug' => 'hosting-and-support',
                'short_description' => 'Reliable hosting and ongoing support to keep your site running smoothly.',
                'content' => $this->tiptapContent('We provide managed hosting packages with 99.9% uptime, daily backups, security monitoring, and ongoing technical support. Focus on running your business while we take care of the technical side.'),
                'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2',
                'features' => ['Managed cloud hosting', 'Daily automated backups', 'SSL certificates & security', '24/7 uptime monitoring', 'Priority technical support'],
                'cta_text' => 'Get Started',
                'cta_link' => '/contact-us',
                'sort_order' => 4,
                'status' => 'published',
                'published_at' => now()->subWeeks(2),
                'meta_title' => 'Hosting & Support Services',
                'meta_description' => 'Managed hosting with 99.9% uptime and priority support.',
            ],
            [
                'title' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'short_description' => 'Cross-platform mobile apps that your customers will love.',
                'content' => $this->tiptapContent('We are currently expanding our mobile development offering. Stay tuned for more details on our cross-platform app development services.'),
                'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                'features' => ['Cross-platform development', 'Native performance', 'App store submission', 'Push notifications', 'Analytics integration'],
                'cta_text' => 'Enquire Now',
                'cta_link' => '/contact-us',
                'sort_order' => 5,
                'status' => 'draft',
            ],
        ];

        foreach ($services as $service) {
            ServiceModel::create(array_merge($service, [
                'author_id' => $authorId,
            ]));
        }

        // ── Menus ──────────────────────────────────────────
        $mainMenu = MenuModel::create(['name' => 'Main Navigation', 'handle' => 'main']);

        $mainItems = [
            ['label' => 'Home', 'url' => '/', 'sort_order' => 0],
            ['label' => 'About', 'url' => '/pages/about-us', 'sort_order' => 1],
            ['label' => 'Services', 'url' => '/pages/services', 'sort_order' => 2],
            ['label' => 'Blog', 'url' => '/blog', 'sort_order' => 3],
            ['label' => 'Contact', 'url' => '/pages/contact-us', 'sort_order' => 4],
        ];

        $menuItemModels = [];
        foreach ($mainItems as $item) {
            $menuItemModels[] = MenuItemModel::create(array_merge($item, [
                'menu_id' => $mainMenu->id,
                'type' => 'custom',
                'target' => '_self',
            ]));
        }

        // Sub-items under Services
        $serviceSubItems = [
            ['label' => 'Web Development', 'url' => '/pages/web-development', 'sort_order' => 0],
            ['label' => 'Web Design', 'url' => '/pages/web-design', 'sort_order' => 1],
            ['label' => 'SEO', 'url' => '/pages/seo-services', 'sort_order' => 2],
        ];

        foreach ($serviceSubItems as $sub) {
            MenuItemModel::create(array_merge($sub, [
                'menu_id' => $mainMenu->id,
                'parent_id' => $menuItemModels[2]->id, // Services parent
                'type' => 'custom',
                'target' => '_self',
            ]));
        }

        // Footer menu
        $footerMenu = MenuModel::create(['name' => 'Footer', 'handle' => 'footer']);

        $footerItems = [
            ['label' => 'Privacy Policy', 'url' => '/pages/privacy-policy', 'sort_order' => 0],
            ['label' => 'Terms & Conditions', 'url' => '/pages/terms-and-conditions', 'sort_order' => 1],
            ['label' => 'Contact', 'url' => '/pages/contact-us', 'sort_order' => 2],
        ];

        foreach ($footerItems as $item) {
            MenuItemModel::create(array_merge($item, [
                'menu_id' => $footerMenu->id,
                'type' => 'custom',
                'target' => '_self',
            ]));
        }
    }

    private function tiptapContent(string $text): array
    {
        return [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'content' => [
                        ['type' => 'text', 'text' => $text],
                    ],
                ],
            ],
        ];
    }
}
