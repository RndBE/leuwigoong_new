
<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.js" type="text/javascript"></script>

<script type="text/javascript"> 
	var MQTTbroker = 'mqtt.beacontelemetry.com';
	var MQTTport = 8083;
	var MQTTsubTopic = "arduino-sample";
	var dataTopics = new Array();
	var client = new Paho.MQTT.Client(MQTTbroker, MQTTport,
									  "clientid_" + parseInt(Math.random() * 100, 10));
	client.onMessageArrived = onMessageArrived;
	client.onConnectionLost = onConnectionLost;
	console.log(MQTTsubTopic);
	var options = {
		timeout: 3,
		useSSL: true,
		userName : "userlog",
		password : "b34c0n",

		onSuccess: function () {
			client.subscribe(MQTTsubTopic, {qos: 0});
			console.log("mqtt connected");
		},
		onFailure: function (message) {
			console.log(message);
		}
	};
	function onConnectionLost(responseObject) {
	};
	function onMessageArrived(message) {
		var dataLog = message.payloadString;
		console.log(dataLog);
	};
	client.connect(options);

</script>