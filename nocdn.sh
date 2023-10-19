#!/bin/bash

mkdir resources

curl -o resources/bootstrap.min.css       "https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css"
curl -o resources/bootstrap.min.css.map   "https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css.map"
curl -o resources/popper.min.js           "https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
curl -o resources/bootstrap.bundle.min.js "https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"
curl -o resources/jquery-3.6.0.min.js	  "https://code.jquery.com/jquery-3.6.0.min.js"
curl -o resources/jquery-ui-git.js 	      "https://releases.jquery.com/git/ui/jquery-ui-git.js"
