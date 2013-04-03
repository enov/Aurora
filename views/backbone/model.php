<script>


var <?= Aurora_Type::classname($model) ?> = Backbone.Model.extend({
	defaults: <?= AU::json_encode($model)?>,
	urlRoot: '<?= Aurora_API::url($model)?>',
});

</script>