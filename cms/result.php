<?php
require_once('auth.php');

$result = '';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $submit = $_POST['submit'] ?? '';
    if($submit == ''){
        exit('間違った操作');
    }

    $ai = $_POST['ai'] ?? '';
    $apiKey = $_SESSION[$ai] ?? '';
    if($apiKey == ''){
        echo 'Unknown API-key';
        exit;
    }
    $conditions = json_decode($_POST['json'], true);
    $pid = $_POST['pid'] ?? '0';

    if($submit == 'makePage'){

        $prompt = '
        Generate a site structure based on the following conditions.
        Return only structured JSON. No explanation or additional text.

        The JSON structure must be:

        {
          "pages": [
            {
              "pageName": "string",
              "description": "string",
              "importance": true
            }
          ]
        }
        ';
    }

    if($submit == 'makeNavi'){
        $prompt = '
            Generate navigation HTML from the provided JSON array.

            Each item in the array contains:
            - index: number
            - name: string
            - slug: string (example: "/about/download/")

            RULES:
            1. Navigation must be built only with <ul> and <li>.
            2. The root <ul> must use class="nav0".
            3. The second-level <ul> must use class="nav1".
            4. A third level (nav2 or deeper) is strictly forbidden.
            5. Each <li> must follow this format:
               <li p="index"><a data-slug="slug">NAME</a></li>

            SLUG RULES:
            6. Slugs must use **English lowercase letters only**.
            7. Regardless of the language of "name", the slug must be an English word
               that represents or relates to the meaning of the name.
            8. Slugs must always be directory-style paths such as:
               "/xxx/" or "/xxx/yyy/" (hierarchical structure allowed).
            9. Slugs must be **unique**. If two items have similar meanings,
               generate a unique slug such as "/about-company/".
            10. Slugs must not contain uppercase letters, Japanese, or any non-English characters.

            ORDER RULES:
            11. The first item must always be the TOP page.
            12. All other items must be arranged in a natural, common-sense navigation order.

            OUTPUT FORMAT:
            - Return ONLY raw HTML.
            - Do NOT include JSON, explanations, comments, or any extra text.

            EXAMPLE OUTPUT:
            <ul class="nav0">
            <li p="0"><a data-slug="/">TOP</a></li>
            <li p="1"><a data-slug="/about/">ABOUT</a>
                <ul class="nav1">
                    <li p="2"><a data-slug="/about/download/">DOWNLOAD</a></li>
                </ul>
            </li>
            </ul>
        ';
    }


    if($submit == 'makeContent'){

    $prompt = '
        Create the HTML content to be placed inside the <main> element
        based on the site settings, page information, and structures provided below.

        Rules:

        - Return HTML only.
        - Do not return JSON.
        - Do not include explanations, comments, Markdown, or code fences.
        - Do not include <html>, <head>, <body>, <header>, <nav>, <main>, or <footer>.
        - All CSS must be written as inline styles in the HTML.
        - The generated content must be responsive.
        - Avoid using @media rules whenever possible.
        - Use clamp() for responsive font sizes, widths, heights, spacing, and other suitable values.
        - When displaying multiple cards or content blocks horizontally, use flex or grid whenever possible.
        - Follow the overall visual image and design direction described in structures.
        - Use simple semantic HTML tags only.
        - Do not add JavaScript.

        - "html" represents the HTML structure of the entire website.
        - "css" represents the CSS used throughout the entire website.
        - "color" represents the common color definitions used throughout the entire website.
        - When generating additional content, fully understand this overall structure and ensure that text and background colors always provide sufficient contrast to meet Google accessibility evaluations.
        - Pay particular attention to text colors to maintain excellent readability.
        - If no suitable modern color can be determined, do not specify colors.

        Page rules:

        - Let "p" be the current page index.

        - When p = 0, the first content block must begin with:

        <section id="hero">
            <div class="inner">

        - When p ≠ 0, the first content block must be exactly:

        <section id="eyecatch" style="background:#666">
            <div class="inner">
                <h1 style="color:#FFF;font-size:clamp(1.6rem,2.8vw,3rem);">PAGE_NAME</h1>
            </div>
        </section>

        - The eyecatch section and the inner div are fixed.
          The only allowed modification is replacing PAGE_NAME with the current page name.

        - Every content block after the first must use the following structure:

        <section>
            <div class="inner">
                ...
            </div>
        </section>

        - Use h2 for main section headings.
        - Use h3 for subsection headings.
        - Use p for all normal text.
        - Do not place p elements inside h2 or h3.

        Image rules:

        - Every img element must be wrapped inside a figure element.
        - Do not use real image files or external URLs.
        - Every img element must use the following src:
          ../common/svg/photo.svg
        - Every background-image must use:
          ../common/svg/photo.svg
        - Every figure element must include aspect-ratio in its inline style.
        - Every img element must include the following inline styles:

          width:100%;
          height:100%;
          object-fit:cover;

        Table rules:

        - Every table element must be wrapped inside:

        <div class="tablewrap">
            <table>
                ...
            </table>
        </div>

        Inner CSS rules:

        - This restriction applies ONLY to the <div class="inner"> element itself, not to its children.  
        - For the <div class="inner"> element itself (except in the eyecatch section), the only allowed inline styles are flex or grid related properties. Do not apply size, margin, padding, gap, or positioning styles to <div class="inner"> itself — the section/inner wrapper spacing is already defined by the shared template CSS and must not be overridden.  
        - Child elements inside <div class="inner"> (figure, h2, h3, p, div wrappers, etc.) should use margin and/or padding as needed to create clear, readable spacing between elements. Never let images and text sit flush against each other.  

        Content rules:

        - Generate only the content required for the current page.
        - Use the language specified in the site settings.
        - Return only the completed HTML to be inserted inside <main>.

        Final design validation:

        - Whenever a background color is specified for a section or content block, verify the contrast of all text inside it.
        - If the background is dark, use sufficiently light text colors for headings, paragraphs, links, and other text elements.
        - If sufficient contrast cannot be guaranteed, do not specify the background color.
        ';
    }


    if($submit == 'makeColor'){
        $prompt = '
            You are an excellent web designer, especially skilled in adjusting the overall color theme of a website.

            "setting" represents the purpose information of this website.
            "layout" represents the HTML structural elements of the entire site.
            Inside the layout, <v>nav</v> is replaced by the information in "nav:".
            Similarly, <v>main</v> is replaced by the information in "main:".
            "ccolor" represents the brand colors. The brand colors consist of three color values, each with an associated percentage. These percentages indicate the importance of each brand color. However, a value of 0% means that the brand color is considered unset. Please treat these percentages as reference information only.
            "logo" represents the image data.

            For the base colors (body, header, nav, footer, and other structural elements), use hues that are analogous to the brand colors on the color wheel, combined with varied levels of lightness and saturation, to create a cohesive but distinct tone each time.  
            For the accent color used in Button 1, choose a color that is clearly distinguishable from the brand colors while remaining harmonious with the overall design. Do not mechanically choose complementary colors or extremely contrasting hues.  
            For Button 2, choose a modern color that fits naturally with the overall design.  
            For <a> elements, choose colors using your own judgment based on the accent color, the brand colors, and the overall design of the site.  
            Also take into account the language information included in "setting" (which indicates the target country or cultural context), and use it as a reference for color preferences and sensibilities commonly favored in that country or culture.  

            The basic text color must prioritize readability. If a background color is used, ensure strong contrast with the text.

            The common color information used throughout the site is determined by "color:". This becomes the theme. Based on this information, rewrite only the color values of the existing properties inside "color" and determine the theme colors for this website.

            Technical constraints (recommended to keep):

            Never use :root under any circumstances.
            Do not add any explanations about the colors.
            Focus only on rewriting the color values of the existing properties inside "color".
            Whenever you change a background color, you must also check and adjust the existing text color properties used on that background to ensure sufficient contrast.
            ';
    }

    $aiselect = '';
    $ch = '';

    if(!$ai || !$prompt){
        exit;
    }

    switch ($ai) {

        case 'chatgpt':
            $aiselect = 'gpt-5-mini';
            $ch = curl_init('https://api.openai.com/v1/responses');
            break;

        case 'gemini':
            $aiselect = 'gemini-3.5-flash';
            $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/'.$aiselect.':generateContent?key='.$apiKey);
            break;

        case 'claude':
            $aiselect = 'claude-sonnet-5';
            $ch = curl_init('https://api.anthropic.com/v1/messages');
            break;

        default:
            exit;
    }

if($ai == 'chatgpt'){
    $data = [
        'model' => $aiselect,
        'input' => $prompt . "\n\nConditions:\n" . json_encode($conditions, JSON_UNESCAPED_UNICODE)
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT        => 120
    ]);
}

if ($ai == 'gemini') {
        $data = [
            'contents' => [['parts' => [['text' => $prompt . "\n\nConditions:\n" . json_encode($conditions, JSON_UNESCAPED_UNICODE)]]]]];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT        => 120
    ]);
}

if($ai == 'claude'){
    $data = [
        'model'      => $aiselect, // 例: 'claude-sonnet-4-6'
        'max_tokens' => 4000,      // Anthropicは必須パラメータ
        'messages'   => [
            [
                'role'    => 'user',
                'content' => $prompt . "\n\nConditions:\n" . json_encode($conditions, JSON_UNESCAPED_UNICODE)
            ]
        ]
    ];
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'x-api-key: ' . $apiKey,        // Authorizationではなくx-api-key
            'anthropic-version: 2023-06-01', // これも必須
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE),
        CURLOPT_TIMEOUT        => 120
    ]);
}

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);




    if($submit == 'makePage'){
        $json = json_decode($response, true);
        $rawJson = '';
        if ($ai === 'chatgpt') {
            foreach (($json['output'] ?? []) as $output) {
                foreach (($output['content'] ?? []) as $content) {
                    if (($content['type'] ?? '') === 'output_text') {
                        $rawJson = $content['text'] ?? '';
                    }
                }
            }
        }

        if ($ai === 'gemini') {
            $rawJson = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        }

        if ($ai === 'claude') {
            foreach (($json['content'] ?? []) as $content) {
                if (($content['type'] ?? '') === 'text') {
                    $rawJson = $content['text'] ?? '';
                }
            }
             $rawJson = preg_replace('/^```json\s*|\s*```$/m', '', trim($rawJson));
        }

        echo $rawJson;

        if ($rawJson !== '' && json_decode($rawJson, true) !== null) {
            file_put_contents(__DIR__ . '/ai/pages.json', $rawJson);
        }
    }


    // make navi
    if($submit == 'makeNavi'){
        $json = json_decode($response, true);   // ← 必須

        $rawJson = '';
        if ($ai === 'chatgpt') {
            foreach (($json['output'] ?? []) as $output) {
                foreach (($output['content'] ?? []) as $content) {
                    if (($content['type'] ?? '') === 'output_text') {
                        $rawJson = $content['text'] ?? '';
                    }
                }
            }
        }

        if ($ai === 'gemini') {
            $rawJson = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        }

        if ($ai === 'claude') {
            foreach (($json['content'] ?? []) as $content) {
                if (($content['type'] ?? '') === 'text') {
                    $rawJson = $content['text'] ?? '';
                }
            }
             $rawJson = preg_replace('/^```[a-zA-Z]*\s*|\s*```$/m', '', trim($rawJson));
        }

        if ($rawJson !== '') {
            file_put_contents(__DIR__ . '/ai/nav.txt', $rawJson);
        }
    }


    // make content 
    if($submit == 'makeContent'){
        $json = json_decode($response, true);   // ← 必須
        $rawJson = '';

        if ($ai === 'chatgpt') {
            foreach (($json['output'] ?? []) as $output) {
                foreach (($output['content'] ?? []) as $content) {
                    if (($content['type'] ?? '') === 'output_text') {
                        $rawJson = $content['text'] ?? '';
                    }
                }
            }
        }

        if ($ai === 'gemini') {
            $rawJson = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        }

        if ($ai === 'claude') {
            foreach (($json['content'] ?? []) as $content) {
                if (($content['type'] ?? '') === 'text') {
                    $rawJson = $content['text'] ?? '';
                }
            }
        }
        
        if ($rawJson !== '') {
            
            file_put_contents(__DIR__ . '/ai/pages/'.$pid.'.txt', $rawJson);

            $folder = __DIR__ . "/ai/pages/".$pid."/";
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $filename = $folder . date('Y-m-d_H-i-s') . '.txt';
            file_put_contents($filename, $rawJson);
        }

    }

    // make Color
    if($submit == 'makeColor'){
        $json = json_decode($response, true);   // ← 必須
        $rawJson = '';

        if ($ai === 'chatgpt') {
            foreach (($json['output'] ?? []) as $output) {
                foreach (($output['content'] ?? []) as $content) {
                    if (($content['type'] ?? '') === 'output_text') {
                        $rawJson = $content['text'] ?? '';
                    }
                }
            }
        }

        if ($ai === 'gemini') {
            $rawJson = $json['candidates'][0]['content']['parts'][0]['text'] ?? '';
        }

        if ($ai === 'claude') {
            foreach (($json['content'] ?? []) as $content) {
                if (($content['type'] ?? '') === 'text') {
                    $rawJson = $content['text'] ?? '';
                }
            }
            $rawJson = preg_replace('/^```[a-zA-Z]*\s*|\s*```$/m', '', trim($rawJson));
        }

        if ($rawJson !== '') {
            $rawJson = str_replace('\n', "\n", $rawJson);
            file_put_contents(__DIR__ . '/common/css/color.css', $rawJson);

            $folder = __DIR__ . "/common/css/archive/";
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            $filename = $folder . date('Y-m-d_H-i-s') . '.txt';
            file_put_contents($filename, $rawJson);
        }

        // $logo = file_get_contents(__DIR__ .'/../common/img/logo.webp');
        // $conditions['logo'] = $logo;
    }


    // ★ その後に返す
    echo $response;
    exit;

    curl_close($ch);

    if ($response === false) {
        $error = '通信エラー: ' . $curlError;
    } else {
        $json = json_decode($response, true);

        if ($httpCode >= 400) {
            $error = $json['error']['message'] ?? ('APIエラー: HTTP ' . $httpCode);
        } else {
            // JSONそのまま返す
            echo json_encode($json, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

}

?>
