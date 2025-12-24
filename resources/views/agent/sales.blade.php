@extends('agent.layout')

@section('title', 'Ventes')
@section('page-title', 'Gestion des Ventes')

@section('content')
<div x-data="saleModal()" class="p-6 w-full">

    <!-- NEW SALE BUTTON -->
    <div class="mb-4 text-center">
        <button @click="openModal()" class="bg-blue-600 text-white px-6 py-2 rounded">
            + Nouvelle Vente
        </button>
    </div>

    <!-- SALE MODAL -->
    <div x-show="modalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-6xl p-6 rounded shadow-lg" @click.away="closeModal()">
            <h2 class="text-xl font-semibold mb-4 text-center" x-text="editMode ? 'Modifier Vente' : 'Nouvelle Vente'"></h2>

            <form :action="editMode ? `/agent/sales/${editSaleId}` : '{{ route('agent.sales.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- PRODUCTS -->
                <template x-for="(item,index) in items" :key="index">
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <select class="col-span-5 border p-2"
                                :name="`products[${index}][product_id]`"
                                x-model="item.product_id"
                                @change="setPrice($event,index)" required>
                            <option value="">Produit</option>
                            @foreach($products as $stock)
                                <option value="{{ $stock->product_id }}" 
                                        :selected="item.product_id == {{ $stock->product_id }}"
                                        data-price="{{ $stock->product->price }}">
                                    {{ $stock->product->name }}
                                </option>
                            @endforeach
                        </select>

                        <input type="number" class="col-span-2 border p-2"
                               :name="`products[${index}][quantity]`"
                               x-model.number="item.quantity"
                               min="1"
                               @input="calcTotal"
                               required>

                        <input type="text" class="col-span-2 border p-2"
                               :name="`products[${index}][unit_price]`"
                               x-model="item.price" readonly>

                        <input type="text" class="col-span-2 border p-2"
                               x-model="item.total" readonly>

                        <button type="button" class="col-span-1 text-red-600" @click="removeItem(index)">✕</button>
                    </div>
                </template>

                <button type="button" class="text-blue-600 mb-4" @click="addItem()">+ Ajouter produit</button>

                <!-- CLIENT -->
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <input name="customer_name" placeholder="Nom client" class="border p-2" x-model="customer.name" required>
                    <input name="customer_email" placeholder="Email" class="border p-2" x-model="customer.email">
                    <input name="customer_phone" placeholder="Téléphone" class="border p-2" x-model="customer.phone">
                    <input name="customer_address" placeholder="Adresse" class="border p-2" x-model="customer.address">
                </div>

                <select name="invoice_type" class="border p-2 w-full mb-4" x-model="invoiceType" required>
                    <option value="proforma">Proforma</option>
                    <option value="facture">Facture</option>
                </select>

                <div class="text-right font-bold mb-4">
                    TOTAL: <span x-text="grandTotal.toFixed(2)"></span> €
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="closeModal()" class="bg-gray-300 px-4 py-2 rounded">Annuler</button>
                    <button class="bg-green-600 text-white px-4 py-2 rounded">Valider</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SALES TABLE -->
    <div class="bg-white mt-6 p-4 w-full overflow-x-auto">
        <table class="w-full border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">Produit</th>
                    <th class="border p-2">Quantité</th>
                    <th class="border p-2">Total</th>
                    <th class="border p-2">Date</th>
                    <th class="border p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td class="border p-2">{{ $sale->product->name }}</td>
                    <td class="border p-2">{{ $sale->quantity }}</td>
                    <td class="border p-2">{{ number_format($sale->total_price,2) }} €</td>
                    <td class="border p-2">{{ $sale->created_at->format('d/m/Y') }}</td>
                    <td class="border p-2 flex gap-2">
                        <a href="{{ route('agent.sales.pdf', $sale->id) }}" target="_blank" class="bg-blue-600 text-white px-2 py-1 rounded text-sm">PDF</a>

                        <button @click="editSale({{ $sale->id }})" class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">
                            Modifier
                        </button>

                        <form method="POST" action="{{ route('agent.sales.destroy', $sale->id) }}" onsubmit="return confirm('Supprimer cette vente ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded text-sm">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
function saleModal() {
    return {
        modalOpen: false,
        editMode: false,
        editSaleId: null,
        items: [{ product_id:'', quantity:1, price:0, total:0 }],
        grandTotal: 0,
        customer: { name:'', email:'', phone:'', address:'' },
        invoiceType: 'facture',

        openModal() { this.modalOpen = true },
        closeModal() { this.modalOpen = false; this.resetForm() },

        addItem() { this.items.push({ product_id:'', quantity:1, price:0, total:0 }) },
        removeItem(index) { this.items.splice(index,1); this.calcTotal() },

        setPrice(e,index) {
            const selected = e.target.selectedOptions[0];
            this.items[index].price = parseFloat(selected.dataset.price||0);
            this.calcTotal();
        },

        calcTotal() {
            this.grandTotal = 0;
            this.items.forEach(i => {
                i.total = (i.quantity * i.price).toFixed(2);
                this.grandTotal += parseFloat(i.total);
            });
        },

        resetForm() {
            this.editMode = false;
            this.editSaleId = null;
            this.items = [{ product_id:'', quantity:1, price:0, total:0 }];
            this.customer = { name:'', email:'', phone:'', address:'' };
            this.invoiceType = 'facture';
            this.calcTotal();
        },

        editSale(id) {
            fetch(`/agent/sales/${id}/edit`)
                .then(res => res.json())
                .then(data => {
                    this.editMode = true;
                    this.editSaleId = id;
                    this.items = [{
                        product_id: data.product_id,
                        quantity: data.quantity,
                        price: data.unit_price,
                        total: (data.quantity * data.unit_price).toFixed(2)
                    }];
                    this.customer.name = data.customer_name;
                    this.customer.email = data.customer_email;
                    this.customer.phone = data.customer_phone;
                    this.customer.address = data.customer_address;
                    this.invoiceType = data.invoice_type;
                    this.calcTotal();
                    this.modalOpen = true;
                });
        }
    }
}
</script>
@endsection
