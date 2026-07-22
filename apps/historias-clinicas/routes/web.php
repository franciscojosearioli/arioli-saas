<?php

use App\Http\Controllers\SessionController;

Route::middleware('auth')->get('/', function () {
    try {
        $completed = \Illuminate\Support\Facades\DB::table('configuracion_sistema')
            ->value('setup_completed');
        if (!$completed) {
            return view('welcome-install');
        }
    } catch (\Throwable) {
        return view('welcome-install');
    }
    return redirect('/admin/dashboard');
});

Auth::routes(['register' => false]);

Route::post('/session/renew', [SessionController::class, 'renewSession'])->name('session.renew')->middleware('auth');

Route::get('/license', [\App\Http\Controllers\LicenseInfoController::class, 'index'])
    ->name('license.index')
    ->middleware(['auth', '2fa']);

Route::get('/system-version', [\App\Http\Controllers\SystemVersionController::class, 'index'])
    ->name('system-version.index')
    ->middleware(['auth', '2fa']);

Route::post('/system-version/update', [\App\Http\Controllers\SystemVersionController::class, 'requestUpdate'])
    ->name('system-version.update')
    ->middleware(['auth', '2fa']);

// Wizard de configuración inicial (solo instancias comerciales — demos bloqueadas vía EnsureSetupComplete + demo.php)
Route::middleware(['auth', '2fa', 'admin'])->group(function () {
    Route::get('/setup', [\App\Http\Controllers\SetupWizardController::class, 'show'])->name('setup.wizard');
    Route::post('/setup', [\App\Http\Controllers\SetupWizardController::class, 'save'])->name('setup.save');
});

// Public consent signing (no auth — patient receives email link)
Route::get('firma-consentimiento/{token}',  [\App\Http\Controllers\FirmaPublicaController::class, 'show'])->name('consentimiento.firmaPublica')->middleware('capability:consentimientos');
Route::post('firma-consentimiento/{token}', [\App\Http\Controllers\FirmaPublicaController::class, 'guardar'])->name('consentimiento.guardarFirmaPublica')->middleware('capability:consentimientos');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', '2fa', 'admin', 'setup.check']], function () {
    Route::get('/dashboard', 'HomeController@index')->name('dashboard.home');

    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);
    Route::get('audit-log/mark-all-as-read/{userId}', 'AuditLogsController@markAllAsRead')->name('audit-log.markAllAsRead');
    
    // Messenger
    Route::get('messenger', 'MessengerController@index')->name('messenger.index');
    Route::get('messenger/create', 'MessengerController@createTopic')->name('messenger.createTopic');
    Route::post('messenger', 'MessengerController@storeTopic')->name('messenger.storeTopic');
    Route::get('messenger/inbox', 'MessengerController@showInbox')->name('messenger.showInbox');
    Route::get('messenger/outbox', 'MessengerController@showOutbox')->name('messenger.showOutbox');
    Route::get('messenger/{topic}', 'MessengerController@showMessages')->name('messenger.showMessages');
    Route::delete('messenger/{topic}', 'MessengerController@destroyTopic')->name('messenger.destroyTopic');
    Route::post('messenger/{topic}/reply', 'MessengerController@replyToTopic')->name('messenger.reply');
    Route::get('messenger/{topic}/reply', 'MessengerController@showReply')->name('messenger.showReply');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Pacientes
    Route::delete('paciente/destroy', 'PacienteController@massDestroy')->name('paciente.massDestroy');
    Route::resource('paciente', 'PacienteController');
    Route::get('paciente/{id}/historia-clinica', 'PdfController@historiaClinica')->name('paciente.historiaClinica');
    Route::get('paciente/{id}/ficha-paciente', 'PdfController@fichaPaciente')->name('paciente.fichaPaciente');
    Route::get('paciente/{id}/ficha-historia-clinica', 'PdfController@fichaHistoriaClinica')->name('paciente.fichaHistoriaClinica');
    Route::get('paciente/{id}/medicacion-paciente', 'PdfController@medicacionPaciente')->name('paciente.medicacionPaciente');
    Route::get('paciente/{id}/consentimiento-paciente', 'PdfController@consentimientoPaciente')->name('paciente.consentimientoPaciente')->middleware('capability:consentimientos');


    // Datos Clinicos
    Route::delete('datos-clinicos/destroy', 'DatoClinicoController@massDestroy')->name('datos-clinicos.massDestroy');
    Route::resource('datos-clinicos', 'DatoClinicoController');

    // Tutores
    Route::delete('tutores/destroy', 'TutorController@massDestroy')->name('tutores.massDestroy');
    Route::resource('tutores', 'TutorController');

    // Familiares
    Route::delete('familiares/destroy', 'FamiliarController@massDestroy')->name('familiares.massDestroy');
    Route::resource('familiares', 'FamiliarController');

    // Esquemas de Medicaciones
    Route::delete('medicacion/destroy', 'MedicacionController@massDestroy')->name('medicacion.massDestroy');
    Route::resource('medicacion', 'MedicacionController');    
    Route::get('medicacion-esquema', 'PdfController@esquemaMedicacion')->name('medicacion.esquema');


    // Documentos
    Route::delete('documento/destroy', 'DocumentoController@massDestroy')->name('documento.massDestroy');
    Route::post('documento/media', 'DocumentoController@storeMedia')->name('documento.storeMedia');
    Route::post('documento/ckmedia', 'DocumentoController@storeCKEditorImages')->name('documento.storeCKEditorImages');
    Route::resource('documento', 'DocumentoController');



    // Informes
    Route::delete('informe/destroy', 'InformeController@massDestroy')->name('informe.massDestroy');
    Route::resource('informe', 'InformeController');

    // Especialidades
    Route::resource('especialidades', 'EspecialidadController');

    // Tipos de Consentimiento
    Route::resource('tipos-consentimiento', 'TipoConsentimientoController')->middleware('capability:consentimientos');

    // Configuración del sistema
    Route::get('configuracion', 'ConfiguracionSistemaController@edit')->name('configuracion.edit');
    Route::put('configuracion', 'ConfiguracionSistemaController@update')->name('configuracion.update');

    // Informe Tipos
    Route::group(['prefix' => 'informes/tipos', 'as' => 'informes.tipos.'], function() {
        Route::delete('destroy', 'InformeTipoController@massDestroy')->name('massDestroy');
        Route::resource('/', 'InformeTipoController')->parameters(['' => 'grupo']); 
        });

});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
        Route::post('profile/two-factor', 'ChangePasswordController@toggleTwoFactor')->name('password.toggleTwoFactor');
    }
});

Route::group(['as' => 'panel.', 'namespace' => 'Panel', 'middleware' => ['auth', '2fa', 'setup.check']], function () {
    Route::get('/dashboard', 'HomeController@index')->name('home');

    // Mensajería
    Route::get('messenger',               'MessengerController@index')->name('messenger.index');
    Route::get('messenger/create',        'MessengerController@createTopic')->name('messenger.createTopic');
    Route::post('messenger',              'MessengerController@storeTopic')->name('messenger.storeTopic');
    Route::get('messenger/inbox',         'MessengerController@showInbox')->name('messenger.showInbox');
    Route::get('messenger/outbox',        'MessengerController@showOutbox')->name('messenger.showOutbox');
    Route::get('messenger/{topic}',       'MessengerController@showMessages')->name('messenger.showMessages');
    Route::delete('messenger/{topic}',    'MessengerController@destroyTopic')->name('messenger.destroyTopic');
    Route::post('messenger/{topic}/reply','MessengerController@replyToTopic')->name('messenger.reply');
    Route::get('messenger/{topic}/reply', 'MessengerController@showReply')->name('messenger.showReply');

    // Agenda
    Route::get('agenda-citas-paciente/{paciente}', 'AgendaController@citasPorPaciente')->name('agenda.citasPorPaciente');
    Route::get('agenda/eventos', 'AgendaController@eventos')->name('agenda.eventos');
    Route::post('agenda/{agenda}/estado', 'AgendaController@cambiarEstado')->name('agenda.cambiarEstado');
    Route::delete('agenda/{agenda}', 'AgendaController@destroy')->name('agenda.destroy');
    Route::resource('agenda', 'AgendaController')->except(['destroy']);
    
    // Pacientes
    Route::delete('paciente/destroy', 'PacienteController@massDestroy')->name('paciente.massDestroy');
    Route::resource('paciente', 'PacienteController');
    Route::get('paciente/{id}/historia-clinica', 'PdfController@historiaClinica')->name('paciente.historiaClinica');
    Route::get('paciente/{id}/ficha-paciente', 'PdfController@fichaPaciente')->name('paciente.fichaPaciente');
    Route::get('paciente/{id}/ficha-historia-clinica', 'PdfController@fichaHistoriaClinica')->name('paciente.fichaHistoriaClinica');
    Route::get('paciente/{id}/medicacion-paciente', 'PdfController@medicacionPaciente')->name('paciente.medicacionPaciente');
    Route::get('paciente/{id}/consentimiento-paciente', 'PdfController@consentimientoPaciente')->name('paciente.consentimientoPaciente')->middleware('capability:consentimientos');
    Route::get('paciente/{id}/firmar-consentimiento', 'PacienteController@firmarConsentimiento')->name('paciente.firmarConsentimiento')->middleware('capability:consentimientos');
    Route::post('paciente/{id}/firmar-consentimiento', 'PacienteController@guardarConsentimiento')->name('paciente.guardarConsentimiento')->middleware('capability:consentimientos');

    // Consentimientos (multi-type)
    Route::get('paciente/{paciente}/consentimientos/create', 'ConsentimientoController@create')->name('consentimiento.create')->middleware('capability:consentimientos');
    Route::post('paciente/{paciente}/consentimientos', 'ConsentimientoController@store')->name('consentimiento.store')->middleware('capability:consentimientos');
    Route::get('consentimiento/{consentimiento}/firmar-presencial', 'ConsentimientoController@firmarPresencial')->name('consentimiento.firmarPresencial')->middleware('capability:consentimientos');
    Route::post('consentimiento/{consentimiento}/firmar-presencial', 'ConsentimientoController@guardarFirmaPresencial')->name('consentimiento.guardarFirmaPresencial')->middleware('capability:consentimientos');
    Route::post('consentimiento/{consentimiento}/enviar-email', 'ConsentimientoController@enviarEmail')->name('consentimiento.enviarEmail')->middleware('capability:consentimientos');
    Route::post('consentimiento/{consentimiento}/firmar-profesional', 'ConsentimientoController@firmarProfesional')->name('consentimiento.firmarProfesional')->middleware('capability:consentimientos');
    Route::get('consentimiento/{consentimiento}/pdf', 'ConsentimientoController@pdf')->name('consentimiento.pdf')->middleware('capability:consentimientos');
    Route::delete('consentimiento/{consentimiento}', 'ConsentimientoController@destroy')->name('consentimiento.destroy')->middleware('capability:consentimientos');
    
    Route::resource('paciente_inactivo', 'HCBajaController');

    // Informes
    Route::delete('informe/destroy', 'InformeController@massDestroy')->name('informe.massDestroy');
    Route::post('informe/{informe}/firmar', 'InformeController@firmar')->name('informe.firmar');
    Route::get('informe/tipos/{tipo}/plantillas', 'InformeController@plantillasPorTipo')->name('informe.plantillasPorTipo');
    Route::get('informe/plantillas/{plantilla}/preview', 'InformeController@plantillaPreview')->name('informe.plantillaPreview');
    Route::resource('informe', 'InformeController');

    // Recetas
    Route::get('recetas', 'RecetaController@index')->name('recetas.index');
    Route::delete('receta/{receta}', 'RecetaController@destroy')->name('receta.destroy');

    // Medicacion
    Route::delete('medicacion/destroy', 'MedicacionController@massDestroy')->name('medicacion.massDestroy');
    // Place edit route before update to prioritize it
    Route::get('medicacion/{paciente_id}/{fecha}/edit', 'MedicacionController@edit')->name('medicacion.edit');
    Route::put('medicacion/{paciente_id}/{fecha}', 'MedicacionController@update')->name('medicacion.update');
    Route::resource('medicacion', 'MedicacionController')->except(['edit', 'update']);
    Route::get('medicacion-esquema', 'PdfController@esquemaMedicacion')->name('medicacion.esquema');

    
    // Profile
    Route::get('panel/profile', 'ProfileController@index')->name('profile.index');
    Route::post('panel/profile', 'ProfileController@update')->name('profile.update');
    Route::post('panel/profile/destroy', 'ProfileController@destroy')->name('profile.destroy');
    Route::post('panel/profile/password', 'ProfileController@password')->name('profile.password');
    Route::post('profile/toggle-two-factor', 'ProfileController@toggleTwoFactor')->name('profile.toggle-two-factor');
    Route::post('panel/profile/firma', 'ProfileController@updateFirma')->name('profile.firma');
    Route::post('panel/profile/firma/delete', 'ProfileController@deleteFirma')->name('profile.firma.delete');

    // Notificaciones del usuario
    Route::get('notificaciones', 'UserAlertsController@index')->name('notificaciones.index');
    Route::post('notificaciones/read-all', 'UserAlertsController@readAll')->name('notificaciones.readAll');
    Route::post('notificaciones/{id}/read', 'UserAlertsController@readOne')->name('notificaciones.readOne');
});

Route::group(['namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    
    // Two Factor Authentication
    if (file_exists(app_path('Http/Controllers/Auth/TwoFactorController.php'))) {
        Route::get('two-factor', 'TwoFactorController@show')->name('twoFactor.show');
        Route::post('two-factor', 'TwoFactorController@check')->name('twoFactor.check');
        Route::get('two-factor/resend', 'TwoFactorController@resend')->name('twoFactor.resend');
    }
});
