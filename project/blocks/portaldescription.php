<?php
function portaldescription_contents() { 
  $name = getRequestVarString('name','obesity');

  if ($name=="cardio") {
     return "RGD has released its Cardiovascular Disease Portal to provide researchers with easy access to data on genes, QTLs, strain models, biological processes and pathways related to cardiovascular diseases. This resource funded by NHLBI also includes dynamic data analysis tools to make it a one stop resource for cardiovascular researchers. The user chooses a disease category to get a pull-down list of diseases. A single click on a disease will provide a list of related genes, QTLs, and strains as well as a genome wide view of these across the genome via GViewer and access to GBrowse results showing the genes and QTLs within the genomic context. Additional pages for Biological Processes, Pathways and Phenotypes provide one-click access to data of interest. A Tools section and a Links section provide additional resources. ";
  }else if ($name=="nuro") {
     return "RGD has released its Neurological Disease Portal to provide researchers with easy access to data on genes, QTLs, strain models, biological processes and pathways related to neurological diseases. This resource, partially funded by NINDS also includes dynamic data analysis tools to make it a one stop resource for neuroscience researchers. The user chooses a disease category to get a pull-down list of diseases. A single click on a disease will provide a list of related genes, QTLs, and strains as well as a genome wide view of these across the genome via GViewer and access to GBrowse results showing the genes and QTLs within the genomic context. Additional pages for Biological Processes, Pathways and Phenotypes provide one-click access to data of interest. A Tools section and a Links section provide additional resources. ";
  }else if ($name == "obesity") {
    return "RGD has released its Obesity / Metabolic Syndrome Portal to provide researchers with easy access to data on genes, QTLs, strain models, biological processes and pathways related to obesity diseases. The user chooses a disease category to get a pull-down list of diseases. A single click on a disease will provide a list of related genes, QTLs, and strains as well as a genome wide view of these across the genome via GViewer and access to GBrowse results showing the genes and QTLs within the genomic context. Additional pages for Biological Processes, Pathways and Phenotypes provide one-click access to data of interest. A Tools section and a Links section provide additional resources.";
  }else if ($name=="cancer") {
    return "The Cancer Disease Portal is an integrated resource for information on genes, QTLs and strains associated with cancer.  The portal provides easy acces to data related to Breast Cancer and Urogenital Cancers.  On the front page, view the results for all the included cancers or choose a disease category to get a pull-down list of diseases. A single click on a disease will provide a list of related genes, QTLs, and strains as well as a genome wide view of these via the GViewer tool.  A link from GViewer to GBrowse shows the genes and QTLs within their genomic context.  Additional pages for Phenotypes, Pathways and Biological Processes provide one-click access to data related to cancer.  Tools, Related Links and Rat Strain Models pages link to additional resources of interest to cancer researchers.";
  }else {
     return ""; 
  }
  
}
?>
