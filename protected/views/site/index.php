<?php
/* @var $this StoreRevenueController */
/* @var $model StoreRevenue */

$this->breadcrumbs = array(
	'Beranda' => array(),

);

$this->menu = array(
	//array('label'=>'List StoreRevenue', 'url'=>array('index')),
	//array('label'=>'Create StoreRevenue', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('omset', {
		data: $(this).serialize()
	});
	return false;
});
");
Yii::app()->clientScript->registerCss('beranda', "
	.beranda div{
		float: left;
		margin: 10px;		
	}
	.beranda div.left {
		width: 25%;
	}
	.beranda div.right {
		width: 70%;
	}
	.beranda div.news {
		width: 98%;
	}
");

$news = Comment::model()->orderByIdDesc()->find();

?>

<h1>Selamat Datang di <i><?php echo CHtml::encode(Yii::app()->name) ?></i></h1>

<p>
	Anda bisa menggunakan operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
	atau <b>=</b>) pada nilai awal pencarian sebagai parameter pembanding.
</p>

<?php echo CHtml::link('Advanced Search', '#', array('class' => 'search-button')); ?>
<div class="search-form" style="display:none">
	<?php $this->renderPartial('_search', array(
		'model' => $model,
	)); ?>
</div><!-- search-form -->
<br /><br />

<div class="form beranda" id="omset">
	<div class="news">
		<fieldset>
			<legend>Berita Terkini: <?php echo $news ? CHtml::encode($news->title) : '—'; ?></legend>
			<?php
			if ($news) {
				echo $news->content;
				$creatorName = $news->creator ? $news->creator->name : '—';
				echo '<p class="right">' . date('d/m/Y H:i:s', (int)$news->timestamp) . ' - ' . CHtml::encode($creatorName) . '</p>';
			} else {
				echo '<p>Tidak ada berita.</p>';
			}
			?>
		</fieldset>
	</div>
	<div class="left">
		<fieldset>
			<legend>Grafik Penjualan</legend>
			<h3 style="text-align: center">DAFTAR OMSET <br />
				PERIODE: <?php echo CHtml::encode($model->getDate() . $model->getDateTo()); ?>
			</h3>
			<?php
			$label = array();
			$data = array();
			$total = 0;
			$temp = array();
			$omset = isset($omset) && is_array($omset) ? $omset : array();
			foreach ($omset as $row) {
				$total += isset($row['omset']) ? (float)$row['omset'] : 0;
			}
			foreach ($omset as $row) {
				$poin = ($total > 0) ? round(((float)(isset($row['omset']) ? $row['omset'] : 0) / $total) * 100, 2) : 0;
				$label[] = isset($row['koalisi']) ? $row['koalisi'] : '—';
				$data[] = $poin;
			}

			$this->widget(
				'chartjs.widgets.ChBars',
				array(
					'width' => 220,
					'height' => 380,
					'labels' => $label,
					'datasets' => array(
						array(
							"fillColor" => "#599fd7",
							"highlightFill" => "#58D3F7",
							"strokeColor" => "#ffffff",
							"data" => $data,
						)
					),
					'options' => array(
						'scaleBeginAtZero' => true,
						'scaleShowHorizontalLines' => true,
						'scaleFontSize' => 16
					)
				)
			);
			?>
		</fieldset>
	</div>
	<div class="right">
		<fieldset>
			<legend>Daftar Omset</legend>
			<?php
			$this->widget('zii.widgets.grid.CGridView', array(
				'id' => 'store-revenue-grid',
				'dataProvider' => $model->search(),
				//'filter'=>$model,
				'columns' => array(
					array(
						'header' => 'No',
						'value' => '$this->grid->dataProvider->pagination->pageSize*$this->grid->dataProvider->pagination->currentPage+$row+1'
					),
					'store_code',
					array(
						'header' => 'Nama Toko',
						'value' => '$data->getStoreName()',
						'footer' => 'TOTAL'
					),

					array(
						'visible' => (Yii::app()->user->checkAccess('owner') || Yii::app()->user->checkAccess('manajer1') || Yii::app()->user->checkAccess('manajer2')),
						'name' => 'current_revenue',
						'value' => 'number_format($data->current_revenue)',
						'htmlOptions' => array('style' => 'text-align:right;font-weight:bold'),
						'footer' => number_format($model->getTotalRevenue()),
						'footerHtmlOptions' => array('style' => 'text-align:right;font-style:normal;font-weight:bold'),
					),
					array(
						'name' => 'point',
						'footer' => '100',
						'htmlOptions' => array('style' => 'text-align:right;font-weight:bold'),
						'footerHtmlOptions' => array('style' => 'text-align:right;font-style:normal;font-weight:bold'),
					),
					array(
						'name' => 'last_updated',
						'value' => 'date("F d, Y H:i:s",$data->last_updated)'
					),
					array(
						'class' => 'CButtonColumn',
						'template' => '{view}'
					),
				),
			)); ?>
		</fieldset>
	</div>

</div>