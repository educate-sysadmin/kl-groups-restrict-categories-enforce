/* Make KLFaT CRUD section submit links keep navigation on CRUD section */
function modifyAction(e) {
    jQuery(e).attr('action','#klam_scheduler_a');
}
jQuery('.klam form.klfat_form').each(function() { modifyAction(this); });
jQuery('.klam form.klfat_control').each(function() { modifyAction(this); });