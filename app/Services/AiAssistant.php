<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

/**
 * Modular AI content assistant (OpenAI).
 *
 * One place for every AI call in Order Click. Add new capabilities as methods
 * or new $action cases in assist() — controllers/views only ever call this class,
 * so swapping model/provider later is a one-file change.
 */
class AiAssistant
{
    /** Is AI configured (key present)? Views use this to show/hide the button. */
    public static function enabled(): bool
    {
        return !empty(config('services.openai.key'));
    }

    /**
     * High-level entry point. Maps a friendly action to a prompt and runs it.
     *
     * @param string $action  improve | translate | grammar | professional | seo | generate
     * @param string $text    the user's current content (or a topic, for "generate")
     * @param array  $opts    ['field' => 'product name', 'target_lang' => 'English', 'context' => '...']
     */
    public function assist(string $action, string $text, array $opts = []): array
    {
        $field = trim((string) ($opts['field'] ?? 'content'));
        $target = trim((string) ($opts['target_lang'] ?? 'English'));
        $context = trim((string) ($opts['context'] ?? ''));
        $text = trim($text);

        // Guard: nothing to work with (except generate, which can start from a field/context).
        if ($text === '' && $action !== 'generate') {
            return ['success' => false, 'error' => 'Please write something first.'];
        }

        $base = "You are a professional copywriter for an online store platform. "
            . "Write natural, appealing, correct copy. Keep it concise and suitable for a {$field}. "
            . "Return ONLY the resulting text — no quotes, no explanations, no labels, no markdown.";

        switch ($action) {
            case 'improve':
                $instructions = "{$base} Improve and polish the following {$field}. Keep the SAME language as the input. Fix grammar and flow, make it clearer and more appealing, keep the original meaning.";
                $input = $text;
                break;

            case 'grammar':
                $instructions = "{$base} Correct spelling and grammar only. Keep the SAME language and meaning; do not rewrite the style.";
                $input = $text;
                break;

            case 'professional':
                $instructions = "{$base} Rewrite the following {$field} in a professional, trustworthy, marketing-ready tone. Keep the SAME language.";
                $input = $text;
                break;

            case 'translate':
                $instructions = "{$base} Translate the following {$field} into {$target}. Keep the tone and meaning; localise naturally rather than word-for-word.";
                $input = $text;
                break;

            case 'seo':
                $instructions = "{$base} Rewrite the following {$field} to be SEO-friendly: include relevant keywords naturally, stay accurate, keep the SAME language. Keep it concise.";
                $input = $text;
                break;

            case 'generate':
                $instructions = "{$base} Generate a professional {$field}"
                    . ($target ? " in {$target}" : '') . ". Base it on this: " . ($context ?: $text);
                $input = $context ?: $text;
                break;

            default:
                return ['success' => false, 'error' => 'Unknown AI action.'];
        }

        return $this->run($instructions, $input);
    }

    /**
     * Generate a full starter store (description, colour, categories, products/services) as structured data.
     * Returns ['success'=>true, 'data'=>[...]] or ['success'=>false,'error'=>...].
     */
    public function buildStore(string $businessType, string $storeName, string $offerings, string $lang = 'English', string $categories = ''): array
    {
        $isBooking = in_array($businessType, ['booking', 'service'], true);
        $unit = $isBooking ? 'bookable services' : 'products';
        $schema = $isBooking
            ? '{"description":"1-2 sentence store description","primary_color":"#1f9d55","categories":["Type A","Type B","Type C"],"items":[{"name":"Service name","category":"Type A","price":10,"duration":"30 min","description":"one line"}]}'
            : '{"description":"1-2 sentence store description","primary_color":"#1f9d55","categories":["Category A","Category B","Category C"],"items":[{"name":"Product name","category":"Category A","price":3.5,"description":"one appealing line"}]}';

        $catRule = $categories !== ''
            ? "Use EXACTLY these categories (in this order): {$categories}. Add 3-4 {$unit} to each."
            : "Choose 3-4 sensible categories; 3-4 {$unit} per category (9-14 total).";

        $instructions = "You are setting up an online store for a {$businessType} business named \"{$storeName}\". "
            . "Generate realistic, appealing starter content. Return ONLY valid minified JSON (no markdown, no code fences) matching EXACTLY this shape: {$schema}. "
            . "Rules: {$catRule} If the merchant listed specific items with prices, include them with those prices; otherwise invent realistic ones. "
            . "Prices are plain numbers; primary_color is a tasteful hex suiting the business; write everything in {$lang}; keep names short and each description to one line.";

        $input = "Business type: {$businessType}\nStore name: {$storeName}"
            . ($categories !== '' ? "\nCategories: {$categories}" : '')
            . "\nWhat they offer: " . ($offerings ?: $businessType);

        $res = $this->run($instructions, $input, 3500);
        if (!$res['success']) {
            return $res;
        }

        $json = $this->parseJson($res['text']);
        if (!is_array($json) || empty($json['items'])) {
            return ['success' => false, 'error' => 'AI could not generate store content. Please try again.'];
        }
        return ['success' => true, 'data' => $json];
    }

    /**
     * Read an uploaded file (menu / product list photo or PDF) and extract the offerings.
     * Also judges whether the file is RELEVANT and CLEAR, so the UI can guide the user.
     *
     * Returns ['success'=>true,'data'=>['relevant'=>bool,'clear'=>bool,'reason'=>string,'offerings'=>string,'categories'=>string]]
     * or ['success'=>false,'error'=>string].
     */
    public function extractFromFile(string $dataUrl, string $mime, string $businessType, string $lang = 'English'): array
    {
        $isPdf = stripos($mime, 'pdf') !== false;

        $instructions = "You help set up an online store for a '{$businessType}' business. "
            . "The user uploaded a file that should be a menu, product list, catalogue or price list. "
            . "STEP 1 — judge the file: is it RELEVANT (an actual menu/products/prices for this kind of business, not a selfie, landscape, screenshot of something unrelated, or a blank/random document)? "
            . "Is it CLEAR enough to read the text and prices (not too blurry, dark, tiny or cropped)? "
            . "STEP 2 — if relevant AND clear, extract every item you can see. "
            . "Return ONLY valid minified JSON (no markdown, no code fences) matching EXACTLY this shape: "
            . '{"relevant":true,"clear":true,"reason":"","offerings":"","categories":""}. '
            . "Rules: 'offerings' = a plain list with ONE ITEM PER LINE separated by real newlines, each line like 'Item name - price' "
            . "(keep prices/numbers exactly as shown, omit the price if there is none; do NOT use angle brackets, placeholders or empty dashes). "
            . "'categories' = a short comma-separated list of the main sections you saw. "
            . "Write 'offerings', 'categories' and 'reason' in {$lang}. "
            . "If NOT relevant, set relevant=false and put a short friendly reason (what the file looks like instead). "
            . "If relevant but NOT clear, set clear=false and a short friendly reason. Never invent items that are not in the file.";

        $content = [['type' => 'input_text', 'text' => 'Read this file and extract the menu / products.']];
        if ($isPdf) {
            $content[] = ['type' => 'input_file', 'filename' => 'upload.pdf', 'file_data' => $dataUrl];
        } else {
            $content[] = ['type' => 'input_image', 'image_url' => $dataUrl];
        }

        $res = $this->runMultimodal($instructions, $content, 3000);
        if (!$res['success']) {
            return $res;
        }
        $data = $this->parseJson($res['text']);
        if (!is_array($data)) {
            return ['success' => false, 'error' => 'Could not read that file. Please try a clearer photo, or type your items manually.'];
        }
        return ['success' => true, 'data' => $data];
    }

    /**
     * OpenAI call with multimodal input (text + image/file content items).
     * Mirrors run() but sends an input message array instead of a plain string.
     */
    public function runMultimodal(string $instructions, array $content, int $maxTokens = 2000, ?string $model = null): array
    {
        $key = config('services.openai.key');
        if (empty($key)) {
            return ['success' => false, 'error' => 'AI is not configured yet.'];
        }
        $model = $model ?: config('services.openai.model');
        $payload = [
            'model' => $model,
            'instructions' => $instructions,
            'input' => [['role' => 'user', 'content' => $content]],
            'max_output_tokens' => $maxTokens,
        ];
        // Only reasoning models (gpt-5*, o*) accept this; others reject the parameter.
        if (preg_match('/^(gpt-5|o\d)/', $model)) {
            $payload['reasoning'] = ['effort' => 'minimal'];
        }
        try {
            $response = Http::withToken($key)
                ->timeout(150)
                ->acceptJson()
                ->post(config('services.openai.endpoint'), $payload);

            if (!$response->successful()) {
                $msg = $response->json('error.message') ?? ('AI request failed (' . $response->status() . ').');
                return ['success' => false, 'error' => $msg];
            }
            $text = $this->extractText($response->json());
            if ($text === '') {
                return ['success' => false, 'error' => 'AI returned an empty response. Please try again.'];
            }
            return ['success' => true, 'text' => $text];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Could not reach the AI service. Please try again.'];
        }
    }

    /** Extract a JSON object from a model response (tolerates code fences / stray prose). */
    public function parseJson(string $text)
    {
        $text = trim($text);
        $text = preg_replace('/```(json)?/i', '', $text);
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }
        return json_decode(substr($text, $start, $end - $start + 1), true);
    }

    /**
     * Core OpenAI call (Responses API). Returns ['success'=>bool, 'text'=>string] or ['success'=>false,'error'=>string].
     */
    public function run(string $instructions, string $input, int $maxTokens = 1500): array
    {
        $key = config('services.openai.key');
        if (empty($key)) {
            return ['success' => false, 'error' => 'AI is not configured yet.'];
        }

        try {
            $response = Http::withToken($key)
                ->timeout(45)
                ->acceptJson()
                ->post(config('services.openai.endpoint'), [
                    'model' => config('services.openai.model'),
                    'instructions' => $instructions,
                    'input' => $input,
                    // GPT-5 mini is a reasoning model — "minimal" skips deep reasoning so it
                    // writes the copy directly (fast + cheap); the budget covers the output text.
                    'reasoning' => ['effort' => 'minimal'],
                    'max_output_tokens' => $maxTokens,
                ]);

            if (!$response->successful()) {
                $msg = $response->json('error.message') ?? ('AI request failed (' . $response->status() . ').');
                return ['success' => false, 'error' => $msg];
            }

            $text = $this->extractText($response->json());
            if ($text === '') {
                return ['success' => false, 'error' => 'AI returned an empty response. Please try again.'];
            }

            return ['success' => true, 'text' => $text];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => 'Could not reach the AI service. Please try again.'];
        }
    }

    /** Defensive parse of the OpenAI Responses API payload. */
    private function extractText($data): string
    {
        if (!is_array($data)) {
            return '';
        }
        // Convenience field returned by some responses.
        if (!empty($data['output_text']) && is_string($data['output_text'])) {
            return trim($data['output_text']);
        }
        $parts = [];
        foreach (($data['output'] ?? []) as $item) {
            if (($item['type'] ?? '') === 'message') {
                foreach (($item['content'] ?? []) as $c) {
                    if (in_array(($c['type'] ?? ''), ['output_text', 'text'], true) && !empty($c['text'])) {
                        $parts[] = $c['text'];
                    }
                }
            }
        }
        return trim(implode("\n", $parts));
    }
}
