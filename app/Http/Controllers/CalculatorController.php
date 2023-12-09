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

        $result = $this->evaluateFormula($formula);

        $this->saveCalculation($formula, $result,$request->userId);

        return response()->json(['result' => $result]);
    }

    private function evaluateFormula($formula)
    {
        $language = new ExpressionLanguage();

        try {
            return $language->evaluate($formula);
        } catch (\Throwable $e) {
            return 'Error in the formula';
        }
    }

    private function saveCalculation($formula, $result, $userId)
    {
        Calculation::create([
            'formula' => $formula,
            'result' => $result,
            'created_by' => $userId]);
    }
}
