<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $options = [
            ['id' => 13, 'option_name' => 'payment-settings.currency_code', 'option_value' => 'BRL'],
            ['id' => 14, 'option_name' => 'payment-settings.currency_symbol', 'option_value' => 'R$'],
            ['id' => 16, 'option_name' => 'STRIPE_PUBLIC_KEY', 'option_value' => 'pk_test_51P3GvQJN9OqqM6ftdVHrFrYb2oDB3W70Qo1AUdPpTLqWreSClCOKbKYNtNobRIsNpjdVqJkKT2gERNeqywfXXnWG00j6rgqeat'],
            ['id' => 17, 'option_name' => 'STRIPE_SECRET_KEY', 'option_value' => 'your_stripe_secret_key_here'],
            ['id' => 18, 'option_name' => 'stripeEnable', 'option_value' => 'Yes'],
            ['id' => 19, 'option_name' => 'paypalEnable', 'option_value' => 'No'],
            ['id' => 21, 'option_name' => 'paypal_email', 'option_value' => 'paypal@email.com'],
            ['id' => 22, 'option_name' => 'admin_email', 'option_value' => 'you@example.org'],
            ['id' => 37, 'option_name' => 'seo_title', 'option_value' => 'Premium Work'],
            ['id' => 38, 'option_name' => 'seo_desc', 'option_value' => 'Live streaming & clips for sale at your fingertips'],
            ['id' => 39, 'option_name' => 'seo_keys', 'option_value' => 'live streaming, clips for sale'],
            ['id' => 40, 'option_name' => 'site_title', 'option_value' => 'Twitcher Title'],
            ['id' => 85, 'option_name' => 'default_storage', 'option_value' => 'public'],
            ['id' => 101, 'option_name' => 'site_entry_popup', 'option_value' => 'No'],
            ['id' => 102, 'option_name' => 'entry_popup_title', 'option_value' => 'Entry popup title'],
            ['id' => 103, 'option_name' => 'entry_popup_message', 'option_value' => 'Entry popup message'],
            ['id' => 104, 'option_name' => 'entry_popup_confirm_text', 'option_value' => 'Continue'],
            ['id' => 105, 'option_name' => 'entry_popup_cancel_text', 'option_value' => 'Cancel'],
            ['id' => 106, 'option_name' => 'entry_popup_awayurl', 'option_value' => 'https://google.com'],
            ['id' => 109, 'option_name' => 'card_gateway', 'option_value' => 'Stripe'],
            ['id' => 118, 'option_name' => 'enableMediaDownload', 'option_value' => 'No'],
            ['id' => 199, 'option_name' => 'site_logo', 'option_value' => '/images/11385374136802e97ec40f6.png'],
            ['id' => 203, 'option_name' => 'token_value', 'option_value' => '0.75'],
            ['id' => 204, 'option_name' => 'min_withdraw', 'option_value' => '500'],
            ['id' => 207, 'option_name' => 'bankEnable', 'option_value' => 'No'],
            ['id' => 208, 'option_name' => 'bankInstructions', 'option_value' => null],
            ['id' => 209, 'option_name' => 'ccbillEnable', 'option_value' => 'No'],
            ['id' => 210, 'option_name' => 'CCBILL_ACC_NO', 'option_value' => null],
            ['id' => 211, 'option_name' => 'CCBILL_SUBACC_NO', 'option_value' => null],
            ['id' => 212, 'option_name' => 'CCBILL_SALT_KEY', 'option_value' => null],
            ['id' => 213, 'option_name' => 'CCBILL_FLEX_FORM_ID', 'option_value' => null],
            ['id' => 214, 'option_name' => 'streamersIdentityRequired', 'option_value' => 'Yes'],
            ['id' => 215, 'option_name' => 'favicon', 'option_value' => '/images/1182139141666affc2b452b.png'],
            ['id' => 216, 'option_name' => 'facebook', 'option_value' => null],
            ['id' => 217, 'option_name' => 'google', 'option_value' => null],
            ['id' => 218, 'option_name' => 'tiktok', 'option_value' => null],
            ['id' => 219, 'option_name' => 'mercado_pago', 'option_value' => 'Yes'],
            ['id' => 221, 'option_name' => 'MERCADO_PUBLIC_KEY', 'option_value' => 'APP_USR-babe810e-9fb8-429d-8c44-7c5cab870484'],
            ['id' => 223, 'option_name' => 'MERCADO_SECRET_KEY', 'option_value' => 'APP_USR-3764253685425328-050818-23272d787c18e2728d887d905d32e484-2431586932'],
            ['id' => 225, 'option_name' => 'pagar_me', 'option_value' => 'No'],
            ['id' => 226, 'option_name' => 'PAGAR_PUBLIC_KEY', 'option_value' => 'pk_GrgAmDGU5aSkPWZa'],
            ['id' => 227, 'option_name' => 'PAGAR_SECRET_KEY', 'option_value' => 'your_pagar_secret_key_here'],
            ['id' => 228, 'option_name' => 'streamers_commission_private_room', 'option_value' => '50'],
            ['id' => 229, 'option_name' => 'streamers_commission_videos', 'option_value' => '70'],
            ['id' => 230, 'option_name' => 'admin_commission_private_room', 'option_value' => '50'],
            ['id' => 231, 'option_name' => 'admin_commission_videos', 'option_value' => '30'],
            ['id' => 232, 'option_name' => 'streamer_commission_photos', 'option_value' => '20'],
            ['id' => 234, 'option_name' => 'admin_commission_photos', 'option_value' => '30'],
            ['id' => 235, 'option_name' => 'streamers_commission_photos', 'option_value' => '70'],
            ['id' => 247, 'option_name' => 'private_room_rental_tokens_per_minute', 'option_value' => '5'],
            ['id' => 237, 'option_name' => 'lang', 'option_value' => 'pt'],
            ['id' => 238, 'option_name' => 'site_logo_footer', 'option_value' => '\'\''],
            ['id' => 239, 'option_name' => 'RTMP_URL', 'option_value' => 'rtmp://love248.com/live'],
            ['id' => 240, 'option_name' => 'PUSHER_APP_ID', 'option_value' => '1785307'],
            ['id' => 241, 'option_name' => 'PUSHER_APP_KEY', 'option_value' => 'e6e2e79c72e871c9fc8e'],
            ['id' => 242, 'option_name' => 'PUSHER_APP_SECRET', 'option_value' => '6f4cba204d5fc89df906'],
            ['id' => 243, 'option_name' => 'PUSHER_APP_CLUSTER', 'option_value' => 'sa1'],
            ['id' => 244, 'option_name' => 'admin_client_id', 'option_value' => '3764253685425328'],
            ['id' => 245, 'option_name' => 'admin_client_secret', 'option_value' => 'Mysl8hhtZiQRQBU8WMLouCcAGkGKIFUZ'],
            ['id' => 246, 'option_name' => 'MERCADO_TEST_ACCESS_TOKEN', 'option_value' => 'APP_USR-3764253685425328-050818-23272d787c18e2728d887d905d32e484-2431586932']
        ];

        // Insert or update options
        foreach ($options as $option) {
            DB::table('options_table')->updateOrInsert(
                ['id' => $option['id']],
                $option
            );
        }

        echo "Options have been created/updated successfully!\n";
    }
}
