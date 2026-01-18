<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules()
    {
        return [
            'orden_id' => 'required|exists:orden_entregas,id',
            'accion' => 'required|in:refund,change',
            'items_devolucion' => 'required|array|min:1',
            'items_devolucion.*.product_id' => [
                'required',
                'exists:orden_entregas_productos,product_id'
            ],
            'items_devolucion.*.cantidad' => 'required|integer|min:1',
            'items_devolucion.*.subtotal' => 'required|numeric|min:0',
            'total_devolucion' => 'required|numeric|min:0',

            // Para refund
            'metodo_devolucion' => 'required_if:accion,refund|in:EFECTIVO,TRANSFERENCIA,PAGOMOVIL,EFECTIVOUSD,TRANSFERENCIACOP',

            // Para change
            'productos_cambio' => 'required_if:accion,change|array|min:1',
            'productos_cambio.*.id' => 'required_with:productos_cambio|exists:inventarios,id',
            'productos_cambio.*.cantidad' => 'required_with:productos_cambio|integer|min:1',
            'productos_cambio.*.precio' => 'required_with:productos_cambio|numeric|min:0',
            'total_cambio' => 'required_if:accion,change|numeric|min:0',
            'diferencia' => 'nullable|numeric',
            'metodo_pago_diferencia' => 'required_if:diferencia,!=,0|in:EFECTIVO,TRANSFERENCIA,PAGOMOVIL,EFECTIVOUSD,TRANSFERENCIACOP',
        ];
    }

    public function messages()
    {
        return [
            'items_devolucion.required' => 'Debe seleccionar al menos un producto para devolver',
            'metodo_devolucion.required_if' => 'Debe seleccionar el método de devolución',
            'productos_cambio.required_if' => 'Debe agregar productos para el cambio',
            'metodo_pago_diferencia.required_if' => 'Debe seleccionar cómo pagar/cobrar la diferencia',
        ];
    }
}
