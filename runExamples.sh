#!/usr/bin/env bash


set -e
# set -x

examples=()
examples+=('example/1_basic_usage_acceptable_input.php')
examples+=('example/2_basic_usage_bad_input.php')
examples+=('example/3_errors_returned_acceptable_input.php')
examples+=('example/4_errors_returned_bad_input.php')
examples+=('example/5_other_validator.php')
examples+=('example/6_open_api_descriptions.php')


for example in "${examples[@]}"
do
   :
   echo "Running example $example"
   echo "========================"
   php $example
   example_exit_code=$?

   if [ "$example_exit_code" -ne "0" ]; then echo "Example [] failed";  exit "$example_exit_code"; fi
done


echo "examples completed without problem"