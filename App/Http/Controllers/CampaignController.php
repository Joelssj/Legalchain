<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\CampaignEmail;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function create(Request $request)
    {
        // Validar la entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'tags' => 'required|array',  
            'tags.*' => 'string',
        ]);

        // Crear la campaña
        $campaign = Campaign::create([
            'id' => (string) Str::uuid(), 
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Buscar las etiquetas
        $tags = Tag::whereIn('name', $request->tags)->get();

        if ($tags->isEmpty()) {
            return response()->json(['message' => 'No se encontraron etiquetas válidas.'], 404);
        }

        // Obtener todos los contactos asociados a al menos una de las etiquetas
        $contacts = Contact::whereHas('tags', function ($query) use ($tags) {
            $query->whereIn('tags.name', $tags->pluck('name'));
        })->get();

        // Verificar si hay contactos para enviar la campaña
        if ($contacts->isEmpty()) {
            return response()->json(['message' => 'No se encontraron contactos asociados a estas etiquetas.'], 404);
        }

        // Asociar los contactos a la campaña
        $campaign->contacts()->attach($contacts);

        // Enviar el correo a los contactos asociados
        foreach ($contacts as $contact) {
            Mail::to($contact->email)->send(new CampaignEmail($campaign, $contact));
        }

        return response()->json([
            'message' => 'Campaña creada y correos enviados exitosamente',
            'campaign' => $campaign->load('contacts')  
        ]);
    }
}




