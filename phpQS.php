<?php
require_once 'vendor/autoload.php';
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=dwikywebapp;AccountKey=BRk/bDsfqNDJOlHgT/OXsnu/RG5PjFW2FENu1KdOGsJEFd7lB/CUdNFHbuPfJTSQashUQq0oJJt5MFj4rzjiCQ==";
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "dwikycontainer";

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");
$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

if (isset($_POST['submit'])) {
	$fileToUpload = strtolower($_FILES["fileToUpload"]["name"]);
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
	header("Location: phpQS.php");
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Analis Foto</title>
  <title>Bootstrap Example</title>
  <style>
.button {
  background-color: #99ffff;
  border: none;
  color: white;
  padding: 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}

.button1 {border-radius: 12px;}
  </style>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"/>
</head>
<body>
	<div>

		<h1 style="color:rgba(255,0,0,0.5); text-align:center">Analisis foto</h1>
    <br><br>
			<form action="phpQS.php" method="post" style="text-align:center" enctype="multipart/form-data">
				<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required=""oninvalid="this.setCustomValidity('Tolong isi dengan File Foto/Gambar')">
        <br>
        <br>
				<input type="submit" name="submit" value="Upload file" >
			</form>
<?php      if($result->getBlobs()!=null){?>

		<table class="table">
			<head>
				<tr>

					<th>Nama File</th>
					<th>URL file</th>
					<th>Action</th>
				</tr>
			</head>
			<tbody>
				<?php
				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr >

              </td>
							<td style="text-align: center; vertical-align: middle;" bgcolor="#fff0b3"><?php echo $blob->getName() ?></td>
							<td style="text-align: center; vertical-align: middle;" bgcolor="#ff8080"><?php echo $blob->getUrl() ?></td>
							<td style="text-align: center; vertical-align: middle;" >
								<form action="analyze.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Analyze foto" class="button button1" >
								</form>
							</td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());
				?>
			</tbody>
		</table>

	</div>
</body>
<?php if(isset($_GET['Cleanup'])){
  try{
      // Delete container.
      echo "Deleting Container".PHP_EOL;
      echo $_GET["containerName"].PHP_EOL;
      echo "<br />";
      $blobClient->deleteContainer($_GET["containerName"]);
  }
  catch(ServiceException $e){
      // Handle exception based on error codes and messages.
      // Error codes and messages are here:
      // http://msdn.microsoft.com/library/azure/dd179439.aspx
      $code = $e->getCode();
      $error_message = $e->getMessage();
      echo $code.": ".$error_message."<br />";
  }
}
}
?>
	<h3>jumlah file : <?php echo sizeof($result->getBlobs())?></h3>
<form method="post" action="phpQS.php?Cleanup&containerName=<?php echo $containerName; ?>">
    <button type="submit">Press to clean up all resources created by this sample</button>
</form>

</html>
