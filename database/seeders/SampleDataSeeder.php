<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Website;
use App\Models\Domain;
use App\Models\Email;
use App\Models\Payment;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample websites
        $websites = [
            [
                'name' => 'E-commerce Store',
                'domain' => 'example-store.com',
                'host_server' => 'DigitalOcean',
                'deployment_date' => '2024-01-15',
                'amount_paid' => 2500.00,
                'status' => 'active',
                'description' => 'Online retail store with payment processing',
                'client_name' => 'John Smith',
                'client_email' => 'john@example-store.com',
            ],
            [
                'name' => 'Portfolio Website',
                'domain' => 'designer-portfolio.net',
                'host_server' => 'Vercel',
                'deployment_date' => '2024-02-20',
                'amount_paid' => 1200.00,
                'status' => 'active',
                'description' => 'Creative portfolio for graphic designer',
                'client_name' => 'Sarah Johnson',
                'client_email' => 'sarah@designer-portfolio.net',
            ],
            [
                'name' => 'Blog Platform',
                'domain' => 'tech-blog.org',
                'host_server' => 'AWS',
                'deployment_date' => '2024-03-10',
                'amount_paid' => 1800.00,
                'status' => 'maintenance',
                'description' => 'Technology blog with CMS',
                'client_name' => 'Mike Chen',
                'client_email' => 'mike@tech-blog.org',
            ],
        ];

        foreach ($websites as $websiteData) {
            $website = Website::create($websiteData);
            
            // Create sample payments for each website
            Payment::create([
                'website_id' => $website->id,
                'amount' => $websiteData['amount_paid'],
                'payment_method' => 'Credit Card',
                'payment_date' => $websiteData['deployment_date'],
                'status' => 'completed',
                'notes' => 'Initial website development payment',
                'receipt_number' => 'RCP-' . date('Y') . '-' . str_pad($website->id, 4, '0', STR_PAD_LEFT),
            ]);
        }

        // Create sample domains
        $domains = [
            [
                'domain_name' => 'example-store.com',
                'registrar' => 'GoDaddy',
                'registration_date' => '2024-01-01',
                'expiry_date' => '2025-01-01',
                'annual_cost' => 15.99,
                'status' => 'active',
                'notes' => 'Primary domain for e-commerce store',
            ],
            [
                'domain_name' => 'designer-portfolio.net',
                'registrar' => 'Namecheap',
                'registration_date' => '2024-02-01',
                'expiry_date' => '2025-02-01',
                'annual_cost' => 12.99,
                'status' => 'active',
                'notes' => 'Portfolio domain',
            ],
            [
                'domain_name' => 'tech-blog.org',
                'registrar' => 'Google Domains',
                'registration_date' => '2024-03-01',
                'expiry_date' => '2025-03-01',
                'annual_cost' => 14.99,
                'status' => 'active',
                'notes' => 'Blog domain',
            ],
        ];

        foreach ($domains as $domainData) {
            Domain::create($domainData);
        }

        // Create sample emails
        $emails = [
            [
                'email_address' => 'admin@example-store.com',
                'provider' => 'Google Workspace',
                'hosting_plan' => 'Business Starter',
                'monthly_cost' => 6.00,
                'start_date' => '2024-01-15',
                'renewal_date' => '2025-01-15',
                'status' => 'active',
                'notes' => 'Primary admin email for e-commerce store',
                'associated_website' => 'example-store.com',
            ],
            [
                'email_address' => 'contact@designer-portfolio.net',
                'provider' => 'Microsoft 365',
                'hosting_plan' => 'Business Basic',
                'monthly_cost' => 6.00,
                'start_date' => '2024-02-20',
                'renewal_date' => '2025-02-20',
                'status' => 'active',
                'notes' => 'Contact email for portfolio',
                'associated_website' => 'designer-portfolio.net',
            ],
            [
                'email_address' => 'editor@tech-blog.org',
                'provider' => 'ProtonMail',
                'hosting_plan' => 'Professional',
                'monthly_cost' => 8.00,
                'start_date' => '2024-03-10',
                'renewal_date' => '2025-03-10',
                'status' => 'active',
                'notes' => 'Editor email for tech blog',
                'associated_website' => 'tech-blog.org',
            ],
        ];

        foreach ($emails as $emailData) {
            Email::create($emailData);
        }
    }
}
