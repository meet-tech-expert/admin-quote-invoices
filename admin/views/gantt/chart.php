<?php
global $wpdb;
$info = array();
$quote_id = '';
if( isset($_REQUEST['quote']) && $_REQUEST['quote'] != ''){
	$quote_id = $_REQUEST['quote'];
	$phasesTable   = $wpdb->prefix.'admin_phases';

		$sql = "SELECT *  FROM {$wpdb->prefix}admin_phases WHERE quote_id IN ($quote_id)";
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		if($wpdb->num_rows > 0){
			foreach($result as $data){
				$start_date = explode('-' , $data['expected_start_date']);
				$end_date 	= explode('-' , $data['expected_end_date']);
				$record = array(
					"id" 		=> $data['id'],
					"name"  	=> $data['name'],
					"resource"  => $data['name'],
					"start_date"=> $data['expected_start_date'],
					"end_date"  => $data['expected_end_date'],
					"duration"  => null,
					"percent"   => 100,
					"depend"    => null
				);
				array_push($info ,$record);
			}
			//print_r($info);
		}
}
?>
<div class="wrap">

	<?php include_once( AQI_ADMIN_VIEW_PATH.'/notification.php'); 	?>

	<div id="poststuff" class="">
		<div id="post-body">
			<h1 class="wp-heading-inline"><?php esc_html_e('Gantt Chart',$this->plugin_name); ?></h1>
			
			<hr class="wp-header-end">
			<a target="_blank" href="admin.php?page=phase&action=add&quote=<?php echo $quote_id;?>"><?php esc_html_e('Add More Phase for this quote',$this->plugin_name); ?></a>
			<br />
			<br />
			<?php if(!empty($info)){ ?>
			<div id="post-body-content">
				<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
				<script type="text/javascript">
				var phase_url = "<?php echo admin_url('admin.php?page=phase&action=edit&id='); ?>";
					google.charts.load('current', {'packages':['gantt']});
					google.charts.setOnLoadCallback(drawChart);
					
					var rec = '<?php echo json_encode($info); ?>';
					var res = jQuery.parseJSON(rec);
					var recArr = [];
					jQuery.each(res,function(idx,vals){
						res[idx].start_date = parseDate(vals.start_date);
						res[idx].end_date = parseDate(vals.end_date);
						
						recArr.push([vals.id ,vals.name,vals.resource,vals.start_date,vals.end_date,vals.duration,vals.percent,vals.depend]);
					});
					
					function parseDate(input) {
					  var parts = input.match(/(\d+)/g);
					  // new Date(year, month [, date [, hours[, minutes[, seconds[, ms]]]]])
					  return new Date(parts[0], parts[1]-1, parts[2]); // months are 0-based
					}
					
					function drawChart() {

							var data = new google.visualization.DataTable();
						data.addColumn('string', 'Task ID');
						data.addColumn('string', 'Task Name');
						data.addColumn('string', 'Resource');
						data.addColumn('date', 'Start Date');
						data.addColumn('date', 'End Date');
						data.addColumn('number', 'Duration');
						data.addColumn('number', 'Percent Complete');
						data.addColumn('string', 'Dependencies');
					
						
						data.addRows(recArr);
						
						var options = {
							height: '950',
							gantt: {
								//criticalPathEnabled: true,
								trackHeight: 35,
								innerGridHorizLine:{
									stroke:'yellow',
									strokeWidth: 2
								},
								//innerGridTrack:{fill: 'red'}
							},
							//backgroundColor:{fill: 'red'},
						};

						var chart = new google.visualization.Gantt(document.getElementById('chart_div'));

						chart.draw(data, options);
						google.visualization.events.addListener(chart, 'select', selectHandler);
						google.visualization.events.addListener(chart, 'ready', readyHandler);
						
						function selectHandler(e){
							var selectedItem = chart.getSelection()[0];
					          if (selectedItem) {
					            var topping = data.getValue(selectedItem.row, 0);
					            var type = data.getValue(selectedItem.row, 2);
					            window.location.href = phase_url + topping;
					          }
						}
						function readyHandler(e){
							//alert('ready');
						}
						
					}
						
				</script>
				<div id="chart_div"></div>

			</div>
			<?php }else{
				echo __( 'No records found' ,$this->plugin_name);
				} ?>
		</div>
	</div>
</div>