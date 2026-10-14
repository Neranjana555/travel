document.querySelectorAll('.tour-editor').forEach(editor=>{
 const list=editor.querySelector('.editable-days');
 function renumber(){list.querySelectorAll('.editable-day').forEach((day,index)=>{day.querySelector('.day-number').textContent=index+1;day.querySelectorAll('[data-field]').forEach(field=>field.name=`days[${index}][${field.dataset.field}]`);day.querySelector('.remove-day').disabled=list.children.length===1;});}
 editor.querySelector('.add-day').addEventListener('click',()=>{if(list.children.length>=60)return;const fragment=document.getElementById('day-template').content.cloneNode(true);list.append(fragment);renumber();list.lastElementChild.querySelector('input').focus();});
 list.addEventListener('click',event=>{if(event.target.closest('.remove-day')&&list.children.length>1){event.target.closest('.editable-day').remove();renumber();}});renumber();
});
document.querySelectorAll('input[type=file]').forEach(input=>input.addEventListener('change',()=>{const file=input.files[0];if(!file){input.setCustomValidity('');return;}if(file.size>5*1024*1024){input.setCustomValidity('Choose an image smaller than 5 MB.');input.reportValidity();return;}input.setCustomValidity('');const image=input.closest('.photo-field').querySelector('img');if(image.dataset.preview)URL.revokeObjectURL(image.dataset.preview);image.dataset.preview=URL.createObjectURL(file);image.src=image.dataset.preview;image.hidden=false;const status=input.closest('.photo-field').querySelector('.poster-status');if(status)status.textContent='New poster preview. Save to publish it.';}));
document.getElementById('new-tour-trigger')?.addEventListener('click',()=>{const panel=document.getElementById('new-tour');panel.open=true;panel.querySelector('[name=title]').focus();});
document.querySelectorAll('.delete-tour-form').forEach(form=>form.addEventListener('submit',event=>{if(!confirm(`Delete “${form.dataset.tourName}” and its complete itinerary from the website?`))event.preventDefault();}));

document.querySelectorAll('.remove-poster').forEach(button=>button.addEventListener('click',event=>{if(!confirm('Remove this itinerary poster from the tour?'))event.preventDefault();}));
