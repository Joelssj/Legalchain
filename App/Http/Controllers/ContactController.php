<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    // Buscar contacto por nombre
    public function searchByName($name)
    {
        $contacts = Contact::where('name', 'like', '%' . $name . '%')->get();
        return response()->json($contacts);
    }

    // Buscar contacto por folio
    public function searchByFolio($folio)
    {
        $contact = Contact::where('folio', $folio)->first();
        return $contact ? response()->json($contact) : response()->json(['message' => 'Contacto no encontrado'], 404);
    }

    // Buscar contacto por correo
    public function searchByEmail($email)
    {
        $contact = Contact::where('email', $email)->first();
        return $contact ? response()->json($contact) : response()->json(['message' => 'Contacto no encontrado'], 404);
    }

    // Buscar contacto por teléfono
    public function searchByPhone($phone)
    {
        $contact = Contact::where('phone', $phone)->first();
        return $contact ? response()->json($contact) : response()->json(['message' => 'Contacto no encontrado'], 404);
    }

    // Buscar contacto por etiqueta
     public function searchByTag($tag)
    {
    // Obtener contactos que tengan la etiqueta
    $contacts = Contact::whereHas('tags', function ($query) use ($tag) {
        $query->where('name', 'like', '%' . $tag . '%');
    })->get();

    // Retornar los contactos encontrados
    return response()->json($contacts);
}


public function campaignHistory($email)
{
    // Validar que el correo sea válido (aunque ya esté en la ruta, podemos validarlo aquí también)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json(['message' => 'Correo electrónico no válido.'], 400);
    }

    // Buscar el contacto por correo
    $contact = Contact::where('email', $email)->first();

    if (!$contact) {
        return response()->json(['message' => 'No se encontró un contacto con ese correo.'], 404);
    }

    // Cargar el historial de campañas asociadas especificando las columnas
    $campaigns = $contact->campaigns()
                         ->select('campaigns.name', 'campaigns.description', 'campaigns.created_at')  // Especificamos que columnas deseamos de campaigns
                         ->get();

    return response()->json([
        'message' => 'Historial de campañas del cliente.',
        'email' => $contact->email, // Mostrar el correo del cliente
        'campaigns' => $campaigns
    ]);
}

    // Método para almacenar un nuevo contacto
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|unique:contacts,email',
            'birthdate' => 'required|date',
            'tags' => 'required|array|min:1',
            'tags.*' => 'required|string|max:50',
        ]);

        $contact = Contact::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'birthdate' => $request->birthdate,
            'folio' => $this->generateFolio(),
            'projects' => 0,
            'campaigns' => 0,
        ]);

        foreach ($request->tags as $tagName) {
            Tag::create([
                'contact_id' => $contact->id,
                'name' => $tagName,
            ]);
        }

        return response()->json(['message' => 'Contacto creado exitosamente', 'contact' => $contact->load('tags')], 201);
    }

    // Generar folio único
    private function generateFolio(): string
    {
        do {
            $folio = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Contact::where('folio', $folio)->exists());

        return $folio;
    }
}









// namespace App\Http\Controllers;

// use App\Models\Contact;
// use App\Models\Tag;
// use Illuminate\Http\Request;
// use Illuminate\Support\Str;

// class ContactController extends Controller
// {
//     /**
//      * Crear un nuevo contacto con sus etiquetas.
//      */
//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'phone' => 'required|string|max:20',
//             'email' => 'required|email|unique:contacts,email',
//             'birthdate' => 'required|date',
//             'label' => 'required|string|max:50',
//             'tags' => 'array',
//             'tags.*' => 'string|max:50',
//         ]);

//         // Crear el contacto
//         $contact = Contact::create([
//             'id' => (string) Str::uuid(),
//             'name' => $request->name,
//             'phone' => $request->phone,
//             'email' => $request->email,
//             'birthdate' => $request->birthdate,
//             'label' => $request->label,
//         ]);

//         // Crear las etiquetas asociadas
//         if ($request->has('tags')) {
//             foreach ($request->tags as $tagName) {
//                 Tag::create([
//                     'id' => (string) Str::uuid(),
//                     'contact_id' => $contact->id,
//                     'name' => $tagName,
//                 ]);
//             }
//         }

//         return response()->json(['message' => 'Contacto creado', 'contact' => $contact->load('tags')], 201);
//     }

//     /**
//      * Buscar y listar contactos con filtros (nombre, correo, teléfono, etiqueta).
//      */
//     public function index(Request $request)
//     {
//         $contacts = Contact::query()->with('tags')
//             ->when($request->name, fn($query, $name) => $query->where('name', 'LIKE', "%$name%"))
//             ->when($request->email, fn($query, $email) => $query->where('email', 'LIKE', "%$email%"))
//             ->when($request->phone, fn($query, $phone) => $query->where('phone', 'LIKE', "%$phone%"))
//             ->when($request->label, fn($query, $label) => $query->where('label', 'LIKE', "%$label%"))
//             ->when($request->tag, function ($query, $tag) {
//                 $query->whereHas('tags', fn($q) => $q->where('name', 'LIKE', "%$tag%"));
//             });

//         return response()->json($contacts->paginate(10));
//     }

//     /**
//      * Mostrar un contacto específico por UUID.
//      */
//     public function show($id)
//     {
//         $contact = Contact::with('tags')->findOrFail($id);
//         return response()->json($contact);
//     }

//     /**
//      * Actualizar un contacto y sus etiquetas.
//      */
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'name' => 'sometimes|string|max:255',
//             'phone' => 'sometimes|string|max:20',
//             'email' => 'sometimes|email|unique:contacts,email,' . $id,
//             'birthdate' => 'sometimes|date',
//             'label' => 'sometimes|string|max:50',
//             'tags' => 'array',
//             'tags.*' => 'string|max:50',
//         ]);

//         $contact = Contact::findOrFail($id);
//         $contact->update($request->only(['name', 'phone', 'email', 'birthdate', 'label']));

//         // Actualizar las etiquetas (eliminamos las anteriores y creamos las nuevas)
//         if ($request->has('tags')) {
//             $contact->tags()->delete();
//             foreach ($request->tags as $tagName) {
//                 Tag::create([
//                     'id' => (string) Str::uuid(),
//                     'contact_id' => $contact->id,
//                     'name' => $tagName,
//                 ]);
//             }
//         }

//         return response()->json(['message' => 'Contacto actualizado', 'contact' => $contact->load('tags')]);
//     }

//     /**
//      * Eliminar un contacto junto con sus etiquetas.
//      */
//     public function destroy($id)
//     {
//         $contact = Contact::findOrFail($id);
//         $contact->tags()->delete();
//         $contact->delete();

//         return response()->json(['message' => 'Contacto eliminado']);
//     }
// }






// namespace App\Http\Controllers;

// use App\Models\Contact;
// use Illuminate\Http\Request;
// use Illuminate\Support\Str;

// class ContactController extends Controller
// {
//     /**
//      * Crear un nuevo contacto.
//      */
//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'phone' => 'required|string|max:20',
//             'email' => 'required|email|unique:contacts,email',
//             'birthdate' => 'required|date',
//             'label' => 'required|string|max:50',
//             'tags' => 'array',
//             'tags.*' => 'string|max:50',
//         ]);

//         $contact = Contact::create([
//             'id' => (string) Str::uuid(),
//             'name' => $request->name,
//             'phone' => $request->phone,
//             'email' => $request->email,
//             'birthdate' => $request->birthdate,
//             'label' => $request->label,
//         ]);


//         return response()->json(['message' => 'Contacto creado', 'contact' => $contact->load('tags')], 201);
//     }

//     /**
//      * Listar y buscar contactos con filtros (nombre, correo, teléfono, etiqueta).
//      */
//     public function index(Request $request)
//     {
//         $contacts = Contact::query()->with('tags')
//             ->when($request->name, fn($query, $name) => $query->where('name', 'LIKE', "%$name%"))
//             ->when($request->email, fn($query, $email) => $query->where('email', 'LIKE', "%$email%"))
//             ->when($request->phone, fn($query, $phone) => $query->where('phone', 'LIKE', "%$phone%"))
//             ->when($request->label, function ($query, $label) {
//                 $query->whereHas('tags', fn($q) => $q->where('name', 'LIKE', "%$label%"));
//             });
    
//         return response()->json($contacts->paginate(10));
//     }
    

//     /**
//      * Mostrar un contacto específico por su UUID.
//      */
//     public function show($id)
//     {
//         $contact = Contact::with('tags')->findOrFail($id);
//         return response()->json($contact);
//     }

//     /**
//      * Actualizar un contacto existente.
//      */
//     public function update(Request $request, $id)
//     {
//         $request->validate([
//             'name' => 'sometimes|string|max:255',
//             'phone' => 'sometimes|string|max:20',
//             'email' => 'sometimes|email|unique:contacts,email,' . $id,
//             'birthdate' => 'sometimes|date',
//             'label' => 'sometimes|string|max:50',
//             'tags' => 'array',
//             'tags.*' => 'string|max:50',
//         ]);

//         $contact = Contact::findOrFail($id);
//         $contact->update($request->only(['name', 'phone', 'email', 'birthdate', 'label']));



//         return response()->json(['message' => 'Contacto actualizado', 'contact' => $contact->load('tags')]);
//     }

//     /**
//      * Eliminar un contacto.
//      */
//     public function destroy($id)
//     {
//         $contact = Contact::findOrFail($id);
//         $contact->tags()->detach();
//         $contact->delete();

//         return response()->json(['message' => 'Contacto eliminado']);
//     }


// }












// namespace App\Http\Controllers;

// use App\Models\Contact;
// use Illuminate\Http\Request;
// use Illuminate\Support\Str;

// class ContactController extends Controller
// {
//     public function store(Request $request)
//     {
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'phone' => 'required|string|max:20',
//             'email' => 'required|email|unique:contacts,email',
//             'birthdate' => 'required|date',
//             'label' => 'required|string|max:50',
//         ]);

//         $contact = Contact::create([
//             'id' => (string) Str::uuid(),
//             'name' => $request->name,
//             'phone' => $request->phone,
//             'email' => $request->email,
//             'birthdate' => $request->birthdate,
//             'label' => $request->label,
//         ]);

//         return response()->json(['message' => 'Contacto creado', 'contact' => $contact], 201);
//     }
// }
