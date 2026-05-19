<?php

namespace App\Http\Requests;

use App\Models\Complaint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreComplaintRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'location'    => ['required', 'string', 'max:255'],
            'image'       => ['required', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->hasAny(['title', 'location', 'image'])) {
                    return;
                }

                $inputText    = $this->title . ' ' . $this->location;
                $uploadedPath = $this->file('image')->getPathname();

                $complaints = Complaint::whereNotIn('status', ['rejected', 'closed'])
                    ->whereNotNull('image_path')
                    ->select('id', 'title', 'location', 'image_path')
                    ->get();

                foreach ($complaints as $complaint) {
                    $textSimilarity = $this->cosineSimilarity(
                        $inputText,
                        $complaint->title . ' ' . $complaint->location
                    );

                    if ($textSimilarity < 0.75) {
                        continue;
                    }

                    $storedPath      = storage_path('app/public/' . $complaint->image_path);
                    $imageSimilarity = $this->imageSimilarity($uploadedPath, $storedPath);

                    if ($imageSimilarity >= 0.75) {
                        $validator->errors()->add(
                            'title',
                            'A very similar complaint already exists (' .
                            round($textSimilarity * 100) . '% text match, ' .
                            round($imageSimilarity * 100) . '% image match). Please check existing reports before submitting.'
                        );
                        break;
                    }
                }
            },
        ];
    }

    private function cosineSimilarity(string $textA, string $textB): float
    {
        $tfA = $this->termFrequency($this->tokenize($textA));
        $tfB = $this->termFrequency($this->tokenize($textB));

        if (empty($tfA) || empty($tfB)) {
            return 0.0;
        }

        $allTokens = array_unique(array_merge(array_keys($tfA), array_keys($tfB)));

        $dot = 0.0; $magA = 0.0; $magB = 0.0;

        foreach ($allTokens as $token) {
            $a = $tfA[$token] ?? 0;
            $b = $tfB[$token] ?? 0;
            $dot  += $a * $b;
            $magA += $a * $a;
            $magB += $b * $b;
        }

        if ($magA === 0.0 || $magB === 0.0) {
            return 0.0;
        }

        return $dot / (sqrt($magA) * sqrt($magB));
    }

    private function imageSimilarity(string $pathA, string $pathB): float
    {
        $pixelsA = $this->loadResizedGrayscale($pathA);
        $pixelsB = $this->loadResizedGrayscale($pathB);

        if (!$pixelsA || !$pixelsB) {
            return 0.0;
        }

        $total    = count($pixelsA);
        $matching = 0;

        for ($i = 0; $i < $total; $i++) {
            if (abs($pixelsA[$i] - $pixelsB[$i]) <= 50) {
                $matching++;
            }
        }

        return $matching / $total;
    }

    private function loadResizedGrayscale(string $path, int $size = 16): ?array
    {
        if (!file_exists($path)) {
            return null;
        }

        $src = match (mime_content_type($path)) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png'  => imagecreatefrompng($path),
            'image/gif'  => imagecreatefromgif($path),
            default      => false,
        };

        if (!$src) {
            return null;
        }

        $resized = imagecreatetruecolor($size, $size);
        imagecopyresampled($resized, $src, 0, 0, 0, 0, $size, $size, imagesx($src), imagesy($src));
        imagedestroy($src);

        $pixels = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $c       = imagecolorsforindex($resized, imagecolorat($resized, $x, $y));
                $pixels[] = (int)(0.299 * $c['red'] + 0.587 * $c['green'] + 0.114 * $c['blue']);
            }
        }

        imagedestroy($resized);
        return $pixels;
    }

    private function tokenize(string $text): array
    {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        return preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }

    private function termFrequency(array $tokens): array
    {
        $tf = [];
        foreach ($tokens as $token) {
            $tf[$token] = ($tf[$token] ?? 0) + 1;
        }
        return $tf;
    }
}
