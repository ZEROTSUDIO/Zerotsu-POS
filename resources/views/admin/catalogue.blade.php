<x-admin.layout>
    {{-- <div id="table" class="page animate-slide-in">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">
                Catalogue
            </h1>
            <p class="text-gray-500">Data Tabel Product</p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-900">
                    Catalogue
                </h2>
                <button
                    class="gradient-gold text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-300 hover:scale-105">
                    <i class="fas fa-plus-circle mr-2"></i>Add New
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-marble-gray to-marble-white">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                ID
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Name
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Email
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Role
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-800">
                                #001
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                John Anderson
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                john.anderson@email.com
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg gradient-gold text-white text-xs font-semibold">Admin</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-semibold">Active</span>
                            </td>
                            <td class="px-6 py-4">
                                <button class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-800">
                                #002
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                Sarah Mitchell
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                sarah.mitchell@email.com
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg bg-ceramic-blue text-white text-xs font-semibold">Editor</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-semibold">Active</span>
                            </td>
                            <td class="px-6 py-4">
                                <button class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-blue-50/50 transition-colors duration-200">
                            <td class="px-6 py-4 text-sm text-gray-800">
                                #003
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                Michael Roberts
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                michael.roberts@email.com
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg bg-ceramic-blue text-white text-xs font-semibold">Viewer</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg bg-yellow-100 text-yellow-700 text-xs font-semibold">Pending</span>
                            </td>
                            <td class="px-6 py-4">
                                <button class="text-blue-600 hover:text-blue-800 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> --}}
    <livewire:catalogues />
</x-admin.layout>
