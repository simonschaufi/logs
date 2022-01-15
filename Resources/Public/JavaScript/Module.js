require(['TYPO3/CMS/Backend/DateTimePicker','TYPO3/CMS/Backend/Input/Clearable'], function(DateTimePicker) {
    document.querySelectorAll('.module .t3js-clearable').forEach(el => el.clearable())
    document.querySelectorAll('.module .t3js-datetimepicker').forEach(el => DateTimePicker.initializeField(el))
});
