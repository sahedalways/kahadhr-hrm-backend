<div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <h5 class="fw-500 mb-0 text-primary">Site Settings</h5>
                    <hr>
                    <form class="row g-3 align-items-center" wire:submit.prevent="save">

                       
                        <div class="col-md-4">
                            <label class="form-label">App Name<span class="text-danger">*</span></label>
                            <input type="text" required autofocus class="form-control" wire:model="site_title"
                                placeholder="Enter App Name">
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">App Logo <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" wire:model="logo" accept="image/*">

                                {{-- Preview --}}
                                <div class="mt-2">
                                    @if ($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" class="img-thumbnail" width="120">
                                    @elseif($old_logo)
                                        <img src="{{ $old_logo }}" class="img-thumbnail" width="120">
                                    @else
                                        <img src="https://via.placeholder.com/120x80?text=No+Logo" class="img-thumbnail"
                                            width="120">
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Favicon <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" wire:model="favicon" accept="image/*">

                                {{-- Preview --}}
                                <div class="mt-2">
                                    @if ($favicon)
                                        <img src="{{ $favicon->temporaryUrl() }}" class="img-thumbnail" width="50">
                                    @elseif($old_favicon)
                                        <img src="{{ $old_favicon }}" class="img-thumbnail" width="50">
                                    @else
                                        <img src="https://via.placeholder.com/120x80?text=No+Logo" class="img-thumbnail"
                                            width="120">
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Hero Image <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" wire:model="hero_image" accept="image/*">

                                {{-- Preview --}}
                                <div class="mt-2">
                                    @if ($hero_image)
                                        <img src="{{ $hero_image->temporaryUrl() }}" class="img-thumbnail"
                                            width="180">
                                    @elseif($old_hero_image)
                                        <img src="{{ $old_hero_image }}" class="img-thumbnail" width="180">
                                    @else
                                        <img src="https://via.placeholder.com/120x80?text=No+Logo" class="img-thumbnail"
                                            width="120">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="col-md-4">
                            <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" required class="form-control" placeholder="Enter Phone Number"
                                wire:model="site_phone_number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" required class="form-control" placeholder="Enter Email"
                                wire:model="site_email">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Copyright Text <span class="text-danger">*</span></label>
                            <input type="text" required class="form-control" placeholder="Enter Copyright Text"
                                wire:model="copyright_text">
                        </div>


                        <div class="col-12 d-flex align-items-center justify-content-end mt-3">
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading wire:target="save">
                                    <i class="fas fa-spinner fa-spin me-2"></i> Saving...
                                </span>
                                <span wire:loading.remove wire:target="save">
                                    Save
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
