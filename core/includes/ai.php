<?php

class AI {

    function __construct() {}

    function options(): void {
        $o = new OPTIONS();
        h4( 'API Options');
        $r = $o->current_region_prefix();
        $ai_models = [
            'disabled' => 'Disabled',
            'gemini' => 'Google Gemini',
            'chatgpt' => 'Open AI ChatGPT',
            'grok' => 'Meta Grok',
            'claude' => 'Anthropic Claude',
            'deepseek' => 'DeepSeek',
            'mistral' => 'Mistral',
        ];
        $form = [
            [ 'i' => $r.'ai_model', 'n' => 'Default AI Model', 'c' => 12, 't' => 'radios', 'v' => 'disabled',  'k' => 1, 'o' => $ai_models, '_i' => 4 ],
        ];
        $o->form( $form, 'row', 1, '', 'Save AI Options', 'save grad', '', [$r.'ai_model'] );
        $o->region_notice();

        // Show AI Model API Options
        global $options;
        $ai = $options[$r.'ai_model'] ?? '';
        if( isset( $ai_models[$ai] ) ) {
            h4( $ai_models[$ai] . ' API Options');
            $sub_form = [
                [ 'i' => $r.$ai.'_key', 'n' => 'Auth Key', 'c' => 8, 'max' => 512 ],
                [ 'i' => $r.$ai.'_model', 'n' => 'AI Model', 'c' => 4, 't' => 's', 'p' => 'Choose...', 'k' => 1, 'o' => $this->models($ai) ],
            ];
            $o->form( $sub_form, 'row', 1, '', 'Save '.$ai_models[$ai].' Options', 'store grad' );
            $o->region_notice();
        }
    }

    function models( string $ai ): array {
        $models = [
            'gemini' => [
                'gemini-2.0-flash' => 'Gemini Gen AI 2.0 Flash',
                'gemini-2.0-flash-lite' => 'Gemini Gen AI 2.0 Flash Lite',
                'gemini-2.5-flash' => 'Gemini Gen AI 2.5 Flash',
                'gemini-2.5-pro' => 'Gemini Gen AI 2.5 Pro',
            ],
            'chatgpt' => [
                'gpt-4.1' => 'GPT 4.1',
                'gpt-4.1-mini' => 'GPT 4.1 Mini',
                'o4-mini' => 'O4 Mini',
                'o3' => 'O3'
            ],
            'grok' => [

            ],
            'claude' => [

            ],
            'deepseek' => [

            ],
            'mistral' => [
                'magistral-small-latest' => 'Magistral Small'
            ]
        ];
        return $models[$ai] ?? [];
    }
}