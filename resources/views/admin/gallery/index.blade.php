@extends('layouts.admin')

@section('title', 'Gallery Management | Kingdom Admin')

@section('content')
<div x-data="galleryManager()"
     @keydown.escape.window="closeAllModals()"
     class="flex flex-col gap-3 h-full">

    <!-- Header -->
    <div class="dashboard-header flex flex-col sm:flex-row sm:items-center justify-between gap-2 shrink-0">
        <div>
            <h1 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                <i data-lucide="image" class="w-5 h-5 text-kingdom-gold"></i>
                Gallery Management
            </h1>
            <p class="text-slate-500 text-xs">Upload, organize, reorder, and delete gallery images and videos directly.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <button @click="openModal('uploadItems')"
                class="bg-[#0F1D33] hover:bg-slate-800 text-white px-3.5 py-2 rounded-xl font-bold text-xs shadow-sm transition-all flex items-center gap-1.5">
                <i data-lucide="upload" class="w-3.5 h-3.5 text-kingdom-gold"></i>
                Upload Photos / Videos
            </button>
        </div>
    </div>

    <!-- Summary Cards (Simplified to 3 columns) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 shrink-0">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3.5 shadow-sm hover:shadow transition-shadow">
            <div class="h-10 w-10 shrink-0 rounded-xl bg-kingdom-navy/5 flex items-center justify-center text-kingdom-navy">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <h4 class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-0.5">Total Items</h4>
                <span class="text-xl font-black text-kingdom-navy leading-none" x-text="stats.total_items"></span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3.5 shadow-sm hover:shadow transition-shadow">
            <div class="h-10 w-10 shrink-0 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                <i data-lucide="image" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <h4 class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-0.5">Images</h4>
                <span class="text-xl font-black text-blue-600 leading-none" x-text="stats.images_count"></span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3.5 shadow-sm hover:shadow transition-shadow">
            <div class="h-10 w-10 shrink-0 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                <i data-lucide="clapperboard" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <h4 class="text-[9px] font-extrabold uppercase tracking-widest text-slate-400 mb-0.5">Videos</h4>
                <span class="text-xl font-black text-purple-600 leading-none" x-text="stats.videos_count"></span>
            </div>
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="w-full bg-white rounded-2xl border border-slate-200 p-4 flex flex-col flex-1 min-h-[450px]">
        
        <!-- Toolbar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-4">
            
            <!-- Search & Filters -->
            <div class="flex items-center gap-2 flex-wrap flex-1">
                <!-- Search input -->
                <div class="relative w-full md:max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                    </span>
                    <input type="text" x-model="searchQuery" @input.debounce.300ms="applyFilters()" placeholder="Search gallery items..."
                           class="w-full pl-9 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-kingdom-navy outline-none focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/5 transition-all">
                    <button x-show="searchQuery" @click="searchQuery = ''; applyFilters()" class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Type Filter -->
                <select x-model="filterType" @change="applyFilters()"
                        class="py-2 px-3 border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700 rounded-xl outline-none focus:bg-white focus:border-kingdom-gold transition-all">
                    <option value="">All Types</option>
                    <option value="image">Images Only</option>
                    <option value="video">Videos Only</option>
                </select>
            </div>

            <!-- View Toggle & Sorting Hint -->
            <div class="flex items-center justify-between md:justify-end gap-3">
                <span x-show="isOrderable()" class="text-[10px] text-amber-600 bg-amber-50 px-2 py-1 rounded-lg border border-amber-100 font-bold flex items-center gap-1">
                    <i data-lucide="info" class="w-3 h-3 shrink-0"></i> Drag items to sort order
                </span>

                <!-- Grid/List View Toggles -->
                <div class="flex bg-slate-50 border border-slate-200 rounded-xl overflow-hidden p-0.5">
                    <button @click="viewMode = 'grid'" class="p-1.5 rounded-lg transition-colors text-slate-500" :class="viewMode === 'grid' ? 'bg-white shadow-sm text-kingdom-navy' : 'hover:bg-slate-100'">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </button>
                    <button @click="viewMode = 'list'" class="p-1.5 rounded-lg transition-colors text-slate-500" :class="viewMode === 'list' ? 'bg-white shadow-sm text-kingdom-navy' : 'hover:bg-slate-100'">
                        <i data-lucide="list" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div x-show="filteredItems.length === 0" class="flex-1 flex flex-col items-center justify-center text-center p-12">
            <div class="w-16 h-16 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 mb-3 shadow-inner">
                <i data-lucide="image-off" class="w-8 h-8"></i>
            </div>
            <h3 class="text-sm font-bold text-slate-800">No gallery items found</h3>
            <p class="text-xs text-slate-500 mt-1 max-w-xs">Upload new photos or videos to start building your gallery.</p>
            <button @click="openModal('uploadItems')" class="mt-4 bg-[#0F1D33] hover:bg-slate-800 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition-all">
                Upload First Item
            </button>
        </div>

        <!-- Bulk Action Panel (Simplified, only Delete) -->
        <div x-show="selectedItems.length > 0"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-kingdom-navy text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-4 z-[90] border border-white/10 shrink-0">
            <span class="text-xs font-bold whitespace-nowrap"><span class="text-kingdom-gold" x-text="selectedItems.length"></span> selected</span>
            <div class="h-4 w-px bg-white/20"></div>
            <button @click="submitBulk('delete')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 rounded-lg text-[10px] font-bold uppercase !text-white transition-colors flex items-center gap-1.5 shadow-md">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                Delete Selected
            </button>
            <button @click="selectedItems = []" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Grid View Mode -->
        <div x-show="viewMode === 'grid' && filteredItems.length > 0"
             class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 pb-4">
            <template x-for="(item, index) in filteredItems" :key="item.id">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col relative group/card hover:shadow-md transition-all duration-300 select-none"
                     :class="draggedIndex === index ? 'opacity-30 border-[#B89955]' : ''"
                     :draggable="isOrderable()"
                     @dragstart="dragstart($event, index)"
                     @dragover.prevent="dragover($event, index)"
                     @drop="drop($event, index)"
                     @dragend="dragend()">
                    
                    <!-- Checkbox Select -->
                    <input type="checkbox" :value="item.id" x-model="selectedItems" @click.stop
                           class="absolute top-3 left-3 z-20 w-4.5 h-4.5 rounded text-kingdom-navy border-slate-300 focus:ring-0 focus:ring-offset-0 cursor-pointer shadow-md bg-white/95 hover:scale-105 transition-all">

                    <!-- Image / Video Cover -->
                    <div @click="openItemPreview(item)" class="aspect-[4/3] bg-slate-100 overflow-hidden relative border-b border-slate-100 cursor-pointer" title="Click to preview">
                        
                        <!-- Image render -->
                        <template x-if="item.type === 'image'">
                            <img :src="item.thumbnail_url" :alt="item.alt_text" onerror="this.src='https://placehold.co/600x400/0F1D33/ffffff?text=Gallery+Image'" class="w-full h-full object-cover group-hover/card:scale-105 transition-transform duration-500 pointer-events-none" loading="lazy">
                        </template>

                        <!-- Video render -->
                        <template x-if="item.type === 'video'">
                            <div class="w-full h-full relative">
                                <template x-if="item.thumbnail_path">
                                    <img :src="item.thumbnail_url" :alt="item.alt_text" onerror="this.src='https://placehold.co/600x400/0F1D33/ffffff?text=Gallery+Video'" class="w-full h-full object-cover pointer-events-none">
                                </template>
                                <template x-if="!item.thumbnail_path">
                                    <video :src="item.url + '#t=0.001'" preload="metadata" muted class="w-full h-full object-cover pointer-events-none"></video>
                                </template>
                                <!-- Play Overlay badge -->
                                <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                    <div class="w-10 h-10 rounded-full bg-white/90 text-kingdom-navy flex items-center justify-center shadow-lg group-hover/card:scale-110 transition-transform duration-300">
                                        <i data-lucide="play" class="w-5 h-5 fill-current ml-0.5"></i>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Drag Indicator (only when no filters applied) -->
                        <div x-show="isOrderable()" class="absolute top-3.5 right-3.5 opacity-0 group-hover/card:opacity-100 transition-opacity bg-black/40 text-white rounded-lg p-1 hover:bg-black/60 shadow-md cursor-grab active:cursor-grabbing">
                            <i data-lucide="grip-vertical" class="w-4.5 h-4.5"></i>
                        </div>
                    </div>

                    <!-- Card Info -->
                    <div class="p-3 flex-1 flex flex-col justify-between gap-2">
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-slate-800 truncate" :title="item.title" x-text="item.title"></h3>
                            <!-- Type Badge -->
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wide border flex items-center gap-0.5"
                                      :class="item.type === 'video' ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-blue-50 text-blue-600 border-blue-100'">
                                    <i :data-lucide="item.type === 'video' ? 'video' : 'image'" class="w-2.5 h-2.5"></i>
                                    <span x-text="item.type"></span>
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-50 pt-2 shrink-0">
                            <span class="text-[9px] text-slate-400 font-bold uppercase" x-text="formatDate(item.created_at)"></span>
                            <div class="flex items-center gap-1.5">
                                <button @click="openItemPreview(item)" class="p-1 hover:text-kingdom-navy text-slate-400 transition-colors" title="Zoom Preview">
                                    <i data-lucide="zoom-in" class="w-3.5 h-3.5"></i>
                                </button>
                                <button @click="openEditItemModal(item)" class="p-1 hover:text-kingdom-gold text-slate-400 transition-colors" title="Edit Info">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </button>
                                <form :action="`/admin/gallery/items/${item.id}`" method="POST" @submit.prevent="confirmDeleteItem($event)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 hover:text-red-500 text-slate-400 transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- List View Mode -->
        <div x-show="viewMode === 'list' && filteredItems.length > 0"
             class="overflow-x-auto border border-slate-200 rounded-xl pb-2">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th scope="col" class="py-3 px-4 w-12 text-center">
                            <input type="checkbox" @change="toggleSelectAll($event)" :checked="selectedItems.length === filteredItems.length" class="rounded text-kingdom-navy border-slate-300 focus:ring-0">
                        </th>
                        <th scope="col" class="py-3 px-4 w-20">Preview</th>
                        <th scope="col" class="py-3 px-4">Title</th>
                        <th scope="col" class="py-3 px-4">Type</th>
                        <th scope="col" class="py-3 px-4">Upload Date</th>
                        <th scope="col" class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <template x-for="(item, idx) in filteredItems" :key="item.id">
                        <tr class="hover:bg-slate-50/50 transition-colors" :class="draggedIndex === idx ? 'opacity-30' : ''">
                            <td class="py-3 px-4 text-center">
                                <input type="checkbox" :value="item.id" x-model="selectedItems" class="rounded text-kingdom-navy border-slate-300 focus:ring-0">
                            </td>
                            <td class="py-2 px-4">
                                <template x-if="item.type === 'image'">
                                    <img :src="item.thumbnail_url" :alt="item.alt_text" onerror="this.src='https://placehold.co/600x400/0F1D33/ffffff?text=Gallery+Item'" class="w-10 h-10 object-cover rounded-lg border border-slate-200">
                                </template>
                                <template x-if="item.type === 'video'">
                                    <div class="w-10 h-10 relative bg-slate-100 rounded-lg overflow-hidden border border-slate-200">
                                        <template x-if="item.thumbnail_path">
                                            <img :src="item.thumbnail_url" onerror="this.src='https://placehold.co/600x400/0F1D33/ffffff?text=Gallery+Item'" class="w-full h-full object-cover">
                                        </template>
                                        <template x-if="!item.thumbnail_path">
                                            <video :src="item.url + '#t=0.001'" preload="metadata" muted class="w-full h-full object-cover"></video>
                                        </template>
                                        <div class="absolute inset-0 bg-black/10 flex items-center justify-center text-white">
                                            <i data-lucide="play" class="w-3 h-3 fill-current ml-0.5"></i>
                                        </div>
                                    </div>
                                </template>
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800" x-text="item.title"></td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wide border flex items-center gap-0.5 w-max"
                                      :class="item.type === 'video' ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-blue-50 text-blue-600 border-blue-100'">
                                    <i :data-lucide="item.type === 'video' ? 'video' : 'image'" class="w-2.5 h-2.5"></i>
                                    <span x-text="item.type"></span>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-400 font-bold uppercase" x-text="formatDate(item.created_at)"></td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="openItemPreview(item)" class="p-1 hover:text-kingdom-navy text-slate-400 transition-colors" title="Zoom">
                                        <i data-lucide="zoom-in" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <button @click="openEditItemModal(item)" class="p-1 hover:text-kingdom-gold text-slate-400 transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </button>
                                    <form :action="`/admin/gallery/items/${item.id}`" method="POST" @submit.prevent="confirmDeleteItem($event)">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 hover:text-red-500 text-slate-400 transition-colors" title="Delete">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 1. Upload Items Modal -->
    <div x-show="modals.uploadItems" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display:none" x-transition>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden" @click.away="closeModal('uploadItems')">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-kingdom-navy flex items-center gap-1.5">
                        <i data-lucide="upload-cloud" class="w-5 h-5 text-kingdom-gold"></i>
                        Upload Gallery Items
                    </h3>
                    <button @click="closeModal('uploadItems')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form action="{{ route('admin.gallery.upload') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Drag & Drop Upload Zone -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Image / Video Files</label>
                        
                        <div class="border-2 border-dashed border-slate-200 hover:border-kingdom-gold rounded-2xl p-6 bg-slate-50/50 hover:bg-slate-50 transition-colors flex flex-col items-center justify-center text-center cursor-pointer relative"
                             @dragover.prevent="dragOverUpload = true"
                             @dragleave.prevent="dragOverUpload = false"
                             @drop.prevent="handleDropUpload($event)"
                             :class="dragOverUpload ? 'border-kingdom-gold bg-kingdom-gold/5' : ''"
                             @click="$refs.fileInput.click()">
                            
                            <i data-lucide="file-video" class="w-10 h-10 text-slate-400 mb-2"></i>
                            <p class="text-xs font-bold text-slate-700">Drag & Drop files here, or <span class="text-kingdom-gold">Browse</span></p>
                            <p class="text-[10px] text-slate-400 mt-1">Images (JPG, PNG, WEBP) or Videos (MP4, WEBM). Max: <span x-text="formatSize(maxSizeKb * 1024)"></span> per file.</p>
                            
                            <input type="file" x-ref="fileInput" name="files[]" multiple accept="image/jpeg,image/jpg,image/png,image/webp,video/mp4,video/webm" class="hidden" @change="handleFileSelect($event)">
                        </div>
                    </div>

                    <!-- Previews of selected files -->
                    <div x-show="uploadPreviews.length > 0" class="mb-5 flex flex-col gap-2 max-h-[160px] overflow-y-auto border border-slate-100 rounded-xl p-2 bg-slate-50/30 custom-scrollbar">
                        <template x-for="(preview, idx) in uploadPreviews" :key="idx">
                            <div class="flex items-center justify-between p-1.5 border border-slate-100 rounded-lg bg-white shadow-sm gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <template x-if="preview.type === 'image'">
                                        <img :src="preview.src" class="w-8 h-8 object-cover rounded-lg border border-slate-100">
                                    </template>
                                    <template x-if="preview.type === 'video'">
                                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center border border-purple-100 text-purple-600 shrink-0">
                                            <i data-lucide="video" class="w-4 h-4"></i>
                                        </div>
                                    </template>
                                    <div class="min-w-0 text-left">
                                        <p class="text-[10px] font-bold text-slate-700 truncate" x-text="preview.name"></p>
                                        <p class="text-[9px] font-medium mt-0.5" :class="preview.size > maxSizeKb * 1024 ? 'text-red-500 font-bold' : 'text-slate-400'" x-text="formatSize(preview.size)"></p>
                                    </div>
                                </div>
                                <button type="button" @click="removePreview(idx)" class="p-1 text-slate-400 hover:text-red-500 transition-colors">
                                    <i data-lucide="x" class="w-4.5 h-4.5"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <div x-show="hasOverSizedFiles()" class="mb-4 p-3 bg-red-50 border border-red-100 text-red-600 rounded-xl text-xs font-bold flex items-center gap-1.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        One or more files exceed the maximum allowed upload limit of <span x-text="formatSize(maxSizeKb * 1024)"></span>.
                    </div>

                    <div class="flex gap-2">
                        <button type="button" @click="closeModal('uploadItems')" class="flex-1 py-2.5 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-100 transition-colors">Cancel</button>
                        <button type="submit" :disabled="uploadPreviews.length === 0 || hasOverSizedFiles()"
                                class="flex-1 py-2.5 bg-kingdom-navy hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-xl font-bold text-xs shadow-md transition-all active:scale-95">Save Uploads</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 2. Edit Item Modal -->
    <div x-show="modals.editItem" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display:none" x-transition>
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl overflow-hidden" @click.away="closeModal('editItem')">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-bold text-kingdom-navy flex items-center gap-1.5">
                        <i data-lucide="edit" class="w-5 h-5 text-kingdom-gold"></i>
                        Edit Gallery Item
                    </h3>
                    <button @click="closeModal('editItem')" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                
                <form :action="`/admin/gallery/items/${editingItem.id}`" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <input type="hidden" name="sort_order" :value="editingItem.sort_order">

                    <!-- Preview Thumbnail -->
                    <div class="mb-4 flex items-center gap-3 p-2 bg-slate-50 rounded-2xl border border-slate-100">
                        <template x-if="editingItem.type === 'image' || editingItem.thumbnail_path">
                            <img :src="editingItem.thumbnail_url" class="w-16 h-16 object-cover rounded-xl border border-slate-200">
                        </template>
                        <template x-if="editingItem.type === 'video' && !editingItem.thumbnail_path">
                            <div class="w-16 h-16 bg-purple-50 rounded-xl flex items-center justify-center border border-purple-100 text-purple-600">
                                <i data-lucide="video" class="w-6 h-6"></i>
                            </div>
                        </template>
                        <div class="text-left min-w-0 flex-1">
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">File Type</span>
                            <p class="text-xs font-bold text-slate-800 capitalize" x-text="editingItem.type"></p>
                            <p class="text-[9px] text-slate-400 mt-1 truncate" x-text="editingItem.file_path"></p>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="edit_item_title" class="block text-xs font-bold text-slate-700 mb-1">Title</label>
                        <input type="text" name="title" id="edit_item_title" required x-model="editingItem.title"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-kingdom-navy outline-none focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/5 transition-all">
                    </div>

                    <!-- Short Description -->
                    <div class="mb-3">
                        <label for="edit_item_desc" class="block text-xs font-bold text-slate-700 mb-1">Description <span class="text-slate-400 font-normal">(Optional)</span></label>
                        <textarea name="description" id="edit_item_desc" rows="2" x-model="editingItem.description" placeholder="Description of the item..."
                                  class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-kingdom-navy outline-none focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/5 transition-all resize-none"></textarea>
                    </div>

                    <!-- Alt Text -->
                    <div class="mb-5">
                        <label for="edit_item_alt" class="block text-xs font-bold text-slate-700 mb-1">Alt Text (SEO)</label>
                        <input type="text" name="alt_text" id="edit_item_alt" x-model="editingItem.alt_text" placeholder="Alt text keyword..."
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-kingdom-navy outline-none focus:bg-white focus:border-kingdom-gold focus:ring-4 focus:ring-kingdom-gold/5 transition-all">
                    </div>

                    <!-- Custom Thumbnail -->
                    <div class="mb-5">
                        <label for="edit_item_thumb" class="block text-xs font-bold text-slate-700 mb-1">
                            Custom Thumbnail <span class="text-slate-400 font-normal">(Optional, max 2MB)</span>
                        </label>
                        <input type="file" name="thumbnail" id="edit_item_thumb" accept="image/*"
                               class="w-full text-xs font-semibold text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#B89955]/10 file:text-[#B89955] hover:file:bg-[#B89955]/20 cursor-pointer">
                    </div>

                    <div class="flex gap-2">
                        <button type="button" @click="closeModal('editItem')" class="flex-1 py-2.5 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-100 transition-colors">Cancel</button>
                        <button type="submit" class="flex-1 py-2.5 bg-kingdom-navy hover:bg-slate-800 text-white rounded-xl font-bold text-xs shadow-md transition-all active:scale-95">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 3. Lightbox Preview Modal -->
    <div x-show="modals.imagePreview" class="fixed inset-0 z-[150] bg-slate-900/95 flex flex-col items-center justify-center p-4" style="display:none" x-transition>
        <button @click="closeModal('imagePreview')" class="absolute top-6 right-6 text-white/75 hover:text-white hover:scale-110 transition-all p-2 rounded-full bg-white/10 z-[160]" title="Close">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <div class="w-full max-w-3xl flex flex-col items-center justify-center h-full max-h-[80vh] relative">
            <template x-if="previewType === 'image'">
                <img :src="previewUrl" class="max-w-full max-h-full object-contain rounded-xl shadow-2xl border border-white/5">
            </template>
            <template x-if="previewType === 'video'">
                <video :src="previewUrl" controls class="max-w-full max-h-full rounded-xl shadow-2xl border border-white/5"></video>
            </template>
        </div>
        <div class="mt-4 text-center max-w-xl">
            <h3 class="text-lg font-bold text-white" x-text="previewTitle"></h3>
            <p class="text-xs text-slate-400 mt-1" x-text="previewDesc"></p>
        </div>
    </div>

</div>

<!-- Native Javascript Helpers for Admin Gallery Dashboard -->
<script data-navigate-once>
window.galleryManager = function galleryManager() {
    return {
        // Raw Data injected from controller
        stats: @js($stats),
        localImages: @js($items),
        maxSizeKb: @js($maxUploadSizeKb),
        
        // UI states
        viewMode: 'grid',
        searchQuery: '',
        filterType: '',
        selectedItems: [],
        
        // Modal states
        modals: {
            uploadItems: false,
            editItem: false,
            imagePreview: false,
        },
        
        // Active entities for editing / previewing
        editingItem: { id: null, title: '', description: '', alt_text: '', status: '', sort_order: 0, thumbnail_url: '', file_path: '', type: '', thumbnail_path: null },
        previewUrl: '',
        previewType: '',
        previewTitle: '',
        previewDesc: '',
        
        // Drag and Drop helpers
        draggedIndex: null,
        dragOverUpload: false,
        uploadPreviews: [],

        init() {
            // Re-bind Lucide icons on rendering
            this.$watch('viewMode', () => this.$nextTick(() => lucide.createIcons()));
            this.$watch('modals', () => this.$nextTick(() => lucide.createIcons()), { deep: true });
        },

        // Filter calculation computed locally on clientside for instant search responsiveness
        get filteredItems() {
            let list = this.localImages;

            if (this.searchQuery.trim() !== '') {
                const search = this.searchQuery.toLowerCase();
                list = list.filter(item => 
                    (item.title && item.title.toLowerCase().includes(search)) || 
                    (item.description && item.description.toLowerCase().includes(search))
                );
            }

            if (this.filterType !== '') {
                list = list.filter(item => item.type === this.filterType);
            }

            return list;
        },

        isOrderable() {
            return this.searchQuery === '' && this.filterType === '';
        },

        applyFilters() {
            this.selectedItems = [];
            this.$nextTick(() => lucide.createIcons());
        },

        // Open modals
        openModal(name) {
            this.modals[name] = true;
            document.body.style.overflow = 'hidden';
            if (name === 'uploadItems') {
                this.uploadPreviews = [];
            }
        },

        closeModal(name) {
            this.modals[name] = false;
            if (!Object.values(this.modals).some(v => v === true)) {
                document.body.style.overflow = '';
            }
        },

        closeAllModals() {
            Object.keys(this.modals).forEach(k => this.modals[k] = false);
            document.body.style.overflow = '';
        },

        // Item actions
        openEditItemModal(item) {
            this.editingItem = {
                id: item.id,
                title: item.title,
                description: item.description || '',
                alt_text: item.alt_text || '',
                status: item.status,
                sort_order: item.sort_order,
                thumbnail_url: item.thumbnail_url,
                file_path: item.file_path,
                type: item.type,
                thumbnail_path: item.thumbnail_path
            };
            this.openModal('editItem');
        },

        openItemPreview(item) {
            this.previewUrl = item.url;
            this.previewType = item.type;
            this.previewTitle = item.title;
            this.previewDesc = item.description || '';
            this.openModal('imagePreview');
        },

        confirmDeleteItem(event) {
            window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                detail: {
                    title: 'Delete Gallery Item?',
                    message: 'Are you sure you want to permanently delete this item? This action will remove the files from disk and cannot be undone.',
                    onConfirm: () => event.target.submit()
                }
            }));
        },

        // Drag & Drop Image Ordering (Local swap and save)
        dragstart(event, index) {
            if (!this.isOrderable()) {
                event.preventDefault();
                return;
            }
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', index);
            this.draggedIndex = index;
        },

        dragover(event, index) {
            event.preventDefault();
        },

        drop(event, index) {
            const fromIndex = parseInt(event.dataTransfer.getData('text/plain'));
            if (isNaN(fromIndex) || fromIndex === index) return;

            // Get local filtered list
            const currentList = [...this.filteredItems];
            const movedItem = currentList.splice(fromIndex, 1)[0];
            currentList.splice(index, 0, movedItem);

            // Rebuild the master items list with updated sorting order weights
            const orderIds = currentList.map(item => item.id);

            // Update local order weights
            currentList.forEach((item, idx) => {
                const orig = this.localImages.find(i => i.id == item.id);
                if (orig) orig.sort_order = idx;
            });

            // Re-sort local master list
            this.localImages.sort((a, b) => a.sort_order - b.sort_order);

            this.draggedIndex = null;

            // Fire AJAX request to update sort_order in database
            fetch('/admin/gallery/items/reorder', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: orderIds })
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) alert('Failed to save gallery order.');
            })
            .catch(() => alert('Failed to save gallery order.'));
        },

        dragend() {
            this.draggedIndex = null;
        },

        // File upload handling
        handleFileSelect(event) {
            this.addFiles(event.target.files);
        },

        handleDropUpload(event) {
            this.dragOverUpload = false;
            if (event.dataTransfer.files) {
                this.$refs.fileInput.files = event.dataTransfer.files;
                this.addFiles(event.dataTransfer.files);
            }
        },

        addFiles(fileList) {
            for (let i = 0; i < fileList.length; i++) {
                const file = fileList[i];
                const type = file.type.startsWith('video/') ? 'video' : 'image';
                
                if (type === 'image') {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.uploadPreviews.push({
                            name: file.name,
                            size: file.size,
                            type: 'image',
                            src: e.target.result
                        });
                    };
                    reader.readAsDataURL(file);
                } else {
                    this.uploadPreviews.push({
                        name: file.name,
                        size: file.size,
                        type: 'video',
                        src: ''
                    });
                }
            }
        },

        removePreview(idx) {
            this.uploadPreviews.splice(idx, 1);
            if (this.uploadPreviews.length === 0) {
                this.$refs.fileInput.value = '';
            }
        },

        hasOverSizedFiles() {
            return this.uploadPreviews.some(p => p.size > this.maxSizeKb * 1024);
        },

        // Bulk operations
        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedItems = this.filteredItems.map(item => item.id);
            } else {
                this.selectedItems = [];
            }
        },

        submitBulk(action) {
            if (this.selectedItems.length === 0) return;

            window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                detail: {
                    title: 'Confirm Bulk Action',
                    message: `Permanently delete the ${this.selectedItems.length} selected item(s)? This action will remove the files from disk and cannot be undone.`,
                    onConfirm: () => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/admin/gallery/items/bulk';

                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(csrf);

                        const actInput = document.createElement('input');
                        actInput.type = 'hidden';
                        actInput.name = 'action';
                        actInput.value = action;
                        form.appendChild(actInput);

                        this.selectedItems.forEach(id => {
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'ids[]';
                            idInput.value = id;
                            form.appendChild(idInput);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            }));
        },

        // General helpers
        formatSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const d = new Date(dateString);
            return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
    };
}
</script>
@endsection
