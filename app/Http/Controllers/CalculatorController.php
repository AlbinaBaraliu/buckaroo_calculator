<?php

namespace App\Http\Controllers;

use App\Models\Calculation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CalculatorController extends Controller
{

    public function getCalculations($userId): JsonResponse
    {
        $calculations = Calculation::where('created_by', $userId)->orderBy('created_at', 'DESC')->get();

        return response()->json($calculations);
    }

    public function calculate(Request $request): JsonResponse
    {
        $formula = $request->input('formula');

        $result = $this->splitFormula($formula);

        $this->saveCalculation($formula, $result, $request->userId);

        return response()->json(['result' => $result]);
    }
    private function splitFormula($expression): float|int
    {
        $elements = preg_split('/([\+\-\*\/\(\)])/', $expression, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i] == '(') {
                $tempArray = array();
                while ($i < count($elements) && $elements[$i] != ')') {
                    $tempArray[] = $elements[$i];
                    unset($elements[$i]);
                    $i++;
                }
                $elements[$i] = $this->evaluateFormula($tempArray);
                $elements = array_values($elements);
                $i = 0;
            }
        }
        return $this->evaluateFormula($elements);

    }

    private function evaluateFormula($elements): float|int
    {
        $result = 0;
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i] == '/') {
                $result = (float)$elements[$i - 1] / (float)$elements[$i + 1];
                $elements = array_replace($elements, array($i + 1 => $result));
                unset($elements[$i - 1]);
                unset($elements[$i]);
                $i = 0;
            }
            elseif ($elements[$i] == '*') {
                $result = (float)$elements[$i - 1] * (float)$elements[$i + 1];
                $elements = array_replace($elements, array($i + 1 => $result));
                unset($elements[$i - 1]);
                unset($elements[$i]);
                $i = 0;
            }
            $elements = array_values($elements);
        }
        for ($i = 0; $i < count($elements); $i++) {
            if ($elements[$i] == '+') {
                $result = (float)$elements[$i - 1] + (float)$elements[$i + 1];
                $elements = array_replace($elements, array($i + 1 => $result));
                unset($elements[$i]);
                $i = 0;
            }
            elseif ($elements[$i] == '-') {
                if (empty($elements[$i - 1])) {
                    $result = -(float)$elements[$i + 1];
                } else {
                    $result = (float)$elements[$i - 1] - (float)$elements[$i + 1];
                }
                $elements = array_replace($elements, array($i + 1 => $result));
                unset($elements[$i]);
                $i = 0;
            }
            $elements = array_values($elements);

        }
        return $result;
    }

    private function saveCalculation($formula, $result, $userId)
    {
        Calculation::create([
            'formula' => $formula,
            'result' => $result,
            'created_by' => $userId]);
    }
}
