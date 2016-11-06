<?php

function getPhotoPath($photo)
{
	if( ! $photo ) {
		return null;
	}

	return sprintf('https://s3-%s.amazonaws.com/%s/%s', 
			config('filesystems.disks.s3.region'), 
			config('filesystems.disks.s3.bucket'), 
			$photo
	);
}