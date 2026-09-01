<?php
/**
 * social_caption_generator.php
 * Instant caption + hashtag generator used on the Social Media Marketing
 * service detail page. Purely rule-based (no external API, no network
 * calls) — takes a topic/platform/tone and returns ready-to-post captions
 * and a matching hashtag set.
 */

const SOCIAL_PLATFORMS = ['instagram', 'facebook', 'linkedin'];
const SOCIAL_TONES = ['professional', 'casual', 'fun'];

/**
 * @return array ['captions' => string[], 'hashtags' => string[]]
 */
function generate_social_content(string $topic, string $platform, string $tone): array {
    $topic = trim($topic);
    if (!in_array($platform, SOCIAL_PLATFORMS, true)) {
        $platform = 'instagram';
    }
    if (!in_array($tone, SOCIAL_TONES, true)) {
        $tone = 'professional';
    }

    $toneTemplates = [
        'professional' => [
            "Excited to share what we've been working on with {topic}. Here's how it can make a real difference for your business.",
            "{topic} isn't just a trend — it's a smart move for businesses ready to grow. Let's talk about what's possible.",
            "Curious about {topic}? Here's what every business owner should know before getting started.",
            "We've seen firsthand how {topic} can transform results for a business like yours.",
            "Looking to level up with {topic}? Our team breaks down exactly where to start.",
            "The businesses that invest in {topic} today are the ones winning tomorrow.",
            "Here's the truth about {topic}: it works best when it's done right. Let us show you how.",
            "{topic}, made simple — no jargon, just results that matter to your bottom line.",
            "If {topic} has been on your radar, now's the time to make it happen.",
            "Great things happen when {topic} meets the right strategy. Ready to find out how?",
            "{topic} works best with a clear plan behind it. Here's where most businesses start.",
            "Consistency is what separates businesses that win with {topic} from those that don't.",
            "Ask yourself: is {topic} part of your growth plan yet? If not, let's change that.",
            "We build {topic} strategies around your goals, not a one-size-fits-all template.",
            "Data doesn't lie — {topic} done right shows up directly in your bottom line.",
            "Your competitors are already exploring {topic}. Here's how to stay ahead of them.",
            "{topic} pays off when it's backed by the right expertise. That's where we come in.",
            "Every successful brand has a {topic} story. We'd love to help write yours.",
            "Let's turn {topic} from a to-do list item into a measurable result.",
            "A strong {topic} approach today is what sets up tomorrow's growth.",
            "We don't guess with {topic} — every decision is backed by real data.",
            "{topic} shouldn't feel overwhelming. We break it down into a clear roadmap.",
            "The best time to get serious about {topic} was yesterday. The next best time is now.",
            "Businesses that treat {topic} as a priority tend to outperform the ones that don't.",
            "Want to see what {topic} could look like for your brand? Let's map it out together.",
        ],
        'casual' => [
            "Okay but can we talk about {topic} for a sec? Here's why it's kind of a big deal.",
            "{topic} hits different when you actually see the results. Swipe to see what we mean.",
            "Real talk: {topic} could be the missing piece for your brand. Let's chat!",
            "So... we tried {topic} and honestly? Kind of obsessed with the results.",
            "Not gonna lie, {topic} was a total game changer for us. Here's why.",
            "POV: you finally figured out {topic} and everything just clicks.",
            "We need to talk about {topic}. Like, right now. Here's the tea.",
            "Low-key, {topic} might be exactly what your brand's been missing.",
            "Y'all weren't ready for what {topic} can actually do. Let's get into it.",
            "This is your sign to finally try {topic}. You're welcome.",
            "Nobody: ... Us: let's talk about {topic} again because it's THAT good.",
            "Honestly {topic} kind of lives in our heads rent-free right now.",
            "If you're sleeping on {topic}, this is your wake-up call.",
            "We're not saying {topic} will change everything. We're just saying... it might.",
            "Tell us you need {topic} without telling us you need {topic}.",
            "Me explaining to everyone why {topic} is actually a big deal rn.",
            "Unpopular opinion: {topic} is way more fun than people give it credit for.",
            "Some things are hype. {topic} is not one of them — it just works.",
            "Currently obsessed with what {topic} is doing for brands like yours.",
            "Friendly reminder that {topic} exists and it could save you a lot of stress.",
            "We put {topic} to the test so you don't have to. Verdict: worth it.",
            "Quick PSA: {topic} is easier to get right than you'd think.",
            "Not to be dramatic, but {topic} might be the best decision you make this quarter.",
            "Y'know what's underrated? {topic}. Let's fix that.",
            "This post is your sign to stop overthinking {topic} and just start.",
        ],
        'fun' => [
            "{topic} + your brand = a match made in marketing heaven.",
            "Plot twist: {topic} is way easier (and more fun) than you think.",
            "We put the fun in functional — check out what {topic} can do for you!",
            "Warning: {topic} may cause sudden bursts of business growth.",
            "Breaking news: {topic} just walked in and stole the spotlight.",
            "{topic}? Say less. We've got you covered.",
            "New rule: if it involves {topic}, we're already in.",
            "{topic} called — it wants to be part of your next big win.",
            "Guess who just leveled up with {topic}? Could be you.",
            "Cue the confetti — {topic} is here and it's ready to deliver.",
            "{topic} just entered the chat and honestly, it's showing off.",
            "Achievement unlocked: your brand + {topic} = unstoppable combo.",
            "Fun fact: brands that try {topic} tend to have way more fun growing.",
            "{topic} is basically a cheat code for your marketing. Shh, don't tell everyone.",
            "Roses are red, growth charts are too, once {topic} starts working for you.",
            "Somebody call security, {topic} is stealing the show over here.",
            "{topic}: because 'good enough' was never really your style.",
            "We tried {topic} so hard it should honestly come with a warning label.",
            "Behind every great brand is a slightly chaotic love story with {topic}.",
            "This is your villain-era glow-up, brought to you by {topic}.",
            "Ding ding ding! {topic} just leveled up your whole strategy.",
            "{topic} really said 'let me handle this' and then absolutely delivered.",
            "Main character energy starts with a little help from {topic}.",
            "Spoiler alert: {topic} is the plot twist your marketing needed.",
            "Achievement get: unlocked growth mode with {topic}.",
        ],
    ];

    $templates = $toneTemplates[$tone];
    shuffle($templates);
    $captions = array_map(fn($t) => str_replace('{topic}', $topic, $t), array_slice($templates, 0, 4));

    $words = array_values(array_filter(preg_split('/\s+/', preg_replace('/[^A-Za-z0-9\s]/', '', $topic))));
    $camelTag = '#' . implode('', array_map('ucfirst', array_map('strtolower', $words)));
    $joinedTag = '#' . strtolower(implode('', $words));

    $genericByPlatform = [
        'instagram' => [
            '#SmallBusiness', '#DigitalMarketing', '#MarketingTips', '#GrowYourBusiness', '#BrandAwareness',
            '#SocialMediaMarketing', '#ContentCreator', '#InstaMarketing', '#BusinessGrowth', '#MarketingStrategy',
            '#BrandStory', '#ContentIsKing', '#SocialMediaTips', '#OnlineMarketing', '#EntrepreneurLife',
            '#SmallBusinessOwner', '#BusinessTips', '#MarketingAgency', '#BrandBuilding', '#DigitalStrategy',
            '#CreativeContent', '#BusinessOwner', '#StartupLife', '#MarketingGoals',
        ],
        'facebook' => [
            '#SmallBusiness', '#DigitalMarketing', '#BusinessGrowth', '#MarketingTips', '#LocalBusiness',
            '#BusinessOwner', '#MarketingStrategy', '#CommunitySupport', '#ShopLocal', '#BrandAwareness',
            '#OnlineMarketing', '#SmallBusinessOwner', '#BusinessTips', '#SupportLocalBusiness', '#MarketingAgency',
            '#GrowYourBusiness',
        ],
        'linkedin' => [
            '#DigitalMarketing', '#BusinessGrowth', '#Marketing', '#B2B', '#ProfessionalDevelopment',
            '#MarketingStrategy', '#BusinessStrategy', '#Leadership', '#Entrepreneurship', '#BusinessTips',
            '#DigitalTransformation', '#MarketingLeadership', '#B2BMarketing', '#GrowthStrategy',
            '#BusinessDevelopment', '#ThoughtLeadership',
        ],
    ];

    $maxHashtags = ['instagram' => 15, 'facebook' => 8, 'linkedin' => 6][$platform];

    $topicTags = array_values(array_filter([$camelTag, $joinedTag], fn($h) => strlen($h) > 1));
    $genericPool = $genericByPlatform[$platform];
    shuffle($genericPool);

    $hashtags = array_slice(
        array_values(array_unique(array_merge($topicTags, $genericPool))),
        0,
        $maxHashtags
    );

    return ['captions' => $captions, 'hashtags' => $hashtags];
}
