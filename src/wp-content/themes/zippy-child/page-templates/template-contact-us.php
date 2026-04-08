<?php
/*
Template Name: Template Contact Us
Template Post Type: page
*/

get_header();

$contact_placeholder_text = get_field('contact_placeholder_text') ?: '[ PUT PLACEHOLDER PHOTO HERE FIRST ]';
$contact_page_heading = get_field('contact_page_heading') ?: 'Contact Us';
$contact_hero_image_url = zippy_child_get_acf_image_url(get_field('contact_hero_image'));

$contact_business_name = get_field('contact_business_name') ?: 'Sultana Catering';
$contact_address = get_field('contact_address') ?: "236, TUAS SOUTH AVENUE 2, WEST POINT BIZHUB, SINGAPORE 637223";
$contact_phone_label = get_field('contact_phone_label') ?: 'WhatsApp';
$contact_phone_number = get_field('contact_phone_number') ?: '(65) 6339 3196';
$contact_email_label = get_field('contact_email_label') ?: 'Email';
$contact_email = get_field('contact_email') ?: 'your@email.com';
$contact_hours_heading = get_field('contact_hours_heading') ?: 'Operating Hours';
$contact_hours_text = get_field('contact_hours_text') ?: 'Monday - Sunday';

$contact_faq_title = get_field('contact_faq_title') ?: 'Frequently Asked Questions';
$contact_faq_items = get_field('contact_faq_items') ?: array();
$contact_faq_items = is_array($contact_faq_items) ? $contact_faq_items : array();
$contact_faq_image_small_url = zippy_child_get_acf_image_url(get_field('contact_faq_image_small'));
$contact_faq_image_large_url = zippy_child_get_acf_image_url(get_field('contact_faq_image_large'));
if (empty($contact_faq_items)) {
    $contact_faq_items = array(
        array(
            'question' => 'How far in advance should I place a corporate meal order?',
            'answer' => 'Orders should be placed by the cut-off times: Breakfast by 7:00 AM, Lunch by 9:30 AM, Dinner by 4:30 PM. Advance orders for upcoming days are also accepted.',
        ),
        array(
            'question' => 'What cuisines do you offer?',
            'answer' => 'We provide Western, Malay, and Indian meals, available for lunch, dinner, and buffet catering.',
        ),
        array(
            'question' => 'Do you deliver islandwide?',
            'answer' => 'We deliver to Tuas and nearby industrial areas only.',
        ),
        array(
            'question' => 'Is there a minimum order?',
            'answer' => 'Yes. Minimum order is 30 pax per delivery.',
        ),
    );
}

$contact_cta_text = get_field('contact_cta_text') ?: "If you have additional questions about corporate meal delivery or office catering in Tuas,\nfeel free to reach out to us via WhatsApp. We're happy to assist with menu selection,\nbulk orders, and event planning.";
$contact_cta_button_text = get_field('contact_cta_button_text') ?: 'Whatsapp Now';
$contact_cta_button_url = get_field('contact_cta_button_url') ?: '#';

$phone_href_value = preg_replace('/[^0-9+]/', '', (string) $contact_phone_number);
$phone_href = $phone_href_value !== '' ? 'tel:' . $phone_href_value : '';
$email_address = is_string($contact_email) ? trim($contact_email) : '';
$email_href_value = sanitize_email($email_address);
$email_href = $email_href_value !== '' ? 'mailto:' . $email_href_value : '';

$render_media = static function ($url, $alt, $wrapper_classes = '', $image_classes = 'zippy-contact-media__image') use ($contact_placeholder_text) {
    $wrapper_class = trim('zippy-contact-media ' . $wrapper_classes);
    ?>
    <div class="<?php echo esc_attr($wrapper_class); ?>">
        <?php if ($url !== '') : ?>
            <img class="<?php echo esc_attr($image_classes); ?>" src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr((string) $alt); ?>" loading="lazy" />
        <?php else : ?>
            <div class="zippy-contact-media__placeholder">
                <?php echo esc_html($contact_placeholder_text); ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
};
?>

<div class="zippy-contact zippy-site-container">
    <section class="zippy-contact-hero container">
        <div class="zippy-contact-shell text-center">
            <h1 class="zippy-contact-hero__title"><?php echo esc_html($contact_page_heading); ?></h1>
        </div>

        <div class="zippy-contact-bleed">
            <?php $render_media($contact_hero_image_url, $contact_page_heading, 'zippy-contact-hero__media'); ?>
        </div>
    </section>

    <section class="zippy-contact-details ">
        <div class="zippy-contact-shell container">
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <div class="zippy-contact-details__block">
                        <h2 class="zippy-contact-details__name"><?php echo esc_html($contact_business_name); ?></h2>
                        <div class="zippy-contact-details__text">
                            <?php echo wpautop(wp_kses_post($contact_address)); ?>
                        </div>
                        <div class="zippy-contact-details__links">
                            <?php if ($contact_phone_number !== '') : ?>
                                <p class="zippy-contact-details__line">
                                    <span class="zippy-contact-details__label"><?php echo esc_html($contact_phone_label); ?></span>
                                    <?php if ($phone_href !== '') : ?>
                                        <a class="zippy-contact-details__link" href="<?php echo esc_url($phone_href); ?>"><?php echo esc_html($contact_phone_number); ?></a>
                                    <?php else : ?>
                                        <span><?php echo esc_html($contact_phone_number); ?></span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($email_address !== '') : ?>
                                <p class="zippy-contact-details__line">
                                    <span class="zippy-contact-details__label"><?php echo esc_html($contact_email_label); ?></span>
                                    <?php if ($email_href !== '') : ?>
                                        <a class="zippy-contact-details__link" href="<?php echo esc_url($email_href); ?>"><?php echo esc_html($email_address); ?></a>
                                    <?php else : ?>
                                        <span><?php echo esc_html($email_address); ?></span>
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4 offset-lg-1">
                    <div class="zippy-contact-details__block zippy-contact-details__block--hours">
                        <h2 class="zippy-contact-details__heading"><?php echo esc_html($contact_hours_heading); ?></h2>
                        <div class="zippy-contact-details__text">
                            <?php echo wpautop(wp_kses_post($contact_hours_text)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="zippy-contact-faq">
        <div class="zippy-contact-shell container">
            <div class="row g-4 g-lg-5 align-items-start">
                <div class="col-12 col-lg-7">
                    <h2 class="zippy-contact-faq__title"><?php echo esc_html($contact_faq_title); ?></h2>
                    <div class="zippy-contact-faq__list">
                        <?php foreach ($contact_faq_items as $faq_item) : ?>
                            <?php
                            $faq_question = isset($faq_item['question']) ? trim((string) $faq_item['question']) : '';
                            $faq_answer = isset($faq_item['answer']) ? trim((string) $faq_item['answer']) : '';
                            if ($faq_question === '' && $faq_answer === '') {
                                continue;
                            }
                            ?>
                            <article class="zippy-contact-faq__item">
                                <?php if ($faq_question !== '') : ?>
                                    <h3 class="zippy-contact-faq__question"><?php echo esc_html($faq_question); ?></h3>
                                <?php endif; ?>
                                <?php if ($faq_answer !== '') : ?>
                                    <div class="zippy-contact-faq__answer"><?php echo wpautop(wp_kses_post($faq_answer)); ?></div>
                                <?php endif; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="zippy-contact-faq__visual">
                        <?php $render_media($contact_faq_image_large_url, $contact_faq_title, 'zippy-contact-faq__image zippy-contact-faq__image--large'); ?>
                        <?php $render_media($contact_faq_image_small_url, $contact_faq_title, 'zippy-contact-faq__image zippy-contact-faq__image--small'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="zippy-contact-cta container">
        <div class="zippy-contact-shell">
            <div class="zippy-contact-cta__text">
                <?php echo wpautop(wp_kses_post($contact_cta_text)); ?>
            </div>
            <?php if ($contact_cta_button_text !== '') : ?>
                <a class="zippy-contact-cta__button zippy-button" href="<?php echo esc_url($contact_cta_button_url); ?>">
                    <?php echo esc_html($contact_cta_button_text); ?>
                </a>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php
get_footer();
