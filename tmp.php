<?php 

	/*if((0 XOR 0 XOR 0 XOR 0) == 1)
	{
		echo 1;
	}
	else
	{
		echo 0;
	}*/


echo 'X'.chr(9).'X';


echo dec_to_bin(68,8);


function dec_to_bin($x,$p_num_bits = 8)
{
	// Convert to binary
	$bin_str = decbin($x);
	
	$array_bin = Array();
	echo $bin_str.'<hr />'; 
	
	$substr = -1;
	for($i = $p_num_bits;$i > 0;$i--)
	{
		if(strlen($bin_str)-$p_num_bits <= $i)
		{
			//echo substr($bin_str,$substr,1).'<hr />';
			echo $substr.'<hr />';
		}
		else
		{
			echo strlen($bin_str).' >= '.$i.'<hr />';
		}
		$substr -= 1;
	}
}




function fmt_binary($x,$numbits = 8)
{
	// Convert to binary
	$bin = decbin($x);
	
	$bin = substr(str_repeat(0,$numbits),0,$numbits - strlen($bin)).$bin;
	// Split into x 4-bits long
	$rtnval = '';
	for($x = 0; $x < $numbits/4; $x++)
	{
		$rtnval .= substr($bin,$x*4,4);
	}
	// Get rid of first space.
	return ltrim($rtnval);
} 

?>