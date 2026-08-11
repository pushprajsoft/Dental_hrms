@extends('layouts.app')

@section('title', 'Print Layout Designer')
@section('page-title', 'Print Layout & Invoice Designer')
@section('page-subtitle', 'Upload logo, edit details, and design your invoice layout dynamically')

@section('content')

<style>
    .print-container { max-width: 900px; margin: 0 auto; }
    .print-card { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 24px; border: 1px solid #f1f5f9; }
    .print-card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
    .print-card-header h3 { margin: 0; font-size: 1.2rem; font-weight: 700; color: #123C3A; font-family: 'Outfit', sans-serif; }
    .print-card-header .icon-box { width: 40px; height: 40px; border-radius: 10px; background: #f6f9ff; color: #3FBFAD; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    
    .form-group { display: flex; flex-direction: column; }
    .form-group label { font-size: 0.85rem; font-weight: 600; color: #334155; margin-bottom: 8px; }
    .form-input { width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.95rem; }
    
    /* Logo Upload Styling */
    .logo-upload-box { display: flex; align-items: flex-start; gap: 20px; margin-bottom: 20px; flex-wrap: wrap; }
    .logo-preview { width: 120px; height: 120px; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: #f8fafc; overflow: hidden; flex-shrink: 0; }
    .logo-preview img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .logo-preview i { font-size: 2rem; color: #94a3b8; }
    .upload-btn-wrapper { position: relative; overflow: hidden; display: inline-block; }
    .upload-btn { background: #123C3A; color: white; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; border: none; }
    .upload-btn-wrapper input[type=file] { font-size: 100px; position: absolute; left: 0; top: 0; opacity: 0; cursor: pointer; }
    
    .btn-save { background: #3FBFAD; color: #fff; padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; font-size: 1rem; transition: all 0.2s; }
    .btn-save:hover { background: #17847A; transform: translateY(-2px); }
    
    .tox-tinymce { border-radius: 10px !important; border: 1px solid #e2e8f0 !important; }
</style>

<div class="print-container">
    
    @if(session('success'))
        <div class="alert-clinic" style="background: #D1FAE5; color: #065F46; margin-bottom: 20px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('settings.print_layout.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Hospital Logo & Adjustment Controls -->
        <div class="print-card">
            <div class="print-card-header">
                <div class="icon-box"><i class="fa-solid fa-hospital"></i></div>
                <h3>Company Logo & Settings</h3>
            </div>
            
            <div class="logo-upload-box">
                <div style="display: flex; align-items: center; gap: 20px;">
                    <div class="logo-preview">
                        @if($settings->logo_path && file_exists(public_path($settings->logo_path)))
                            <img src="{{ asset($settings->logo_path) }}" alt="Logo">
                        @else
                            <i class="fa-solid fa-image"></i>
                        @endif
                    </div>
                    <div>
                        <h4 style="margin: 0 0 5px 0; color: #123C3A;">Company Logo</h4>
                        <p style="margin: 0 0 10px 0; font-size: 0.85rem; color: #64748b;">Upload PNG/JPG.</p>
                        <div class="upload-btn-wrapper">
                            <button class="upload-btn" type="button">Choose File</button>
                            <input type="file" name="logo" accept="image/*">
                        </div>
                    </div>
                </div>
                
                <!-- LOGO ADJUSTMENT CONTROLS -->
                <div style="display: flex; flex-direction: column; gap: 12px; flex: 1; min-width: 250px;">
                    <div class="form-group" style="margin: 0;">
                        <label>Logo Size</label>
                        <select name="logo_size" class="form-input">
                            <option value="60" @selected(old('logo_size', $settings->logo_size) == '60')>Small (60px)</option>
                            <option value="80" @selected(old('logo_size', $settings->logo_size) == '80')>Medium (80px)</option>
                            <option value="120" @selected(old('logo_size', $settings->logo_size) == '120')>Large (120px)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label>Logo Position</label>
                        <select name="logo_alignment" class="form-input">
                            <option value="left" @selected(old('logo_alignment', $settings->logo_alignment) == 'left')>Left (Next to Text)</option>
                            <option value="top" @selected(old('logo_alignment', $settings->logo_alignment) == 'top')>Top (Centered above Text)</option>
                            <option value="right" @selected(old('logo_alignment', $settings->logo_alignment) == 'right')>Right (Next to Text)</option>
                            <option value="none" @selected(old('logo_alignment', $settings->logo_alignment) == 'none')>Hide Logo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Word-like Header Editor (For Hospital Name, Address, Phone, etc.) -->
        <div class="print-card">
            <div class="print-card-header">
                <div class="icon-box"><i class="fa-solid fa-file-word"></i></div>
                <h3>Invoice Header (Word-like Editor)</h3>
            </div>
            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">Design your Hospital Name, Address, Phone, and Email here using the full Word-like tools. This will appear as the main header on your invoices.</p>
            <textarea name="header_html" id="header_editor">{{ old('header_html', $settings->header_html) }}</textarea>
        </div>

        <!-- Word-like Footer Editor -->
        <div class="print-card">
            <div class="print-card-header">
                <div class="icon-box"><i class="fa-solid fa-file-word"></i></div>
                <h3>Invoice Footer & Terms (Word-like Editor)</h3>
            </div>
            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">Add terms and conditions, payment details, or thank you notes at the bottom of the receipt.</p>
            <textarea name="footer_html" id="footer_editor">{{ old('footer_html', $settings->footer_html) }}</textarea>
        </div>

        <div style="text-align: right; margin-bottom: 30px;">
            <button type="submit" class="btn-save">
                <i class="fa-solid fa-floppy-disk"></i> Save Layout
            </button>
        </div>

    </form>
</div>

<!-- ==========================================
     TINYMCE WYSIWYG EDITOR (FULL WORD SUITE)
     ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#header_editor, #footer_editor',
        height: 350,
        menubar: 'file edit view insert format tools table help',
        plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons',
        toolbar: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
        toolbar_sticky: true,
        custom_colors: true, 
        content_style: 'body { font-family: Helvetica, Arial, sans-serif; font-size: 14px; }',
        branding: false,
        promotion: false
    });
</script>

@endsection