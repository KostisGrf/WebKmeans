$(function(){

  if (sessionStorage.getItem("apikey") === null) {
    window.location.href = "./login.html";
    return;
}

$('#gear-dropdown').show();
var apikey=sessionStorage.getItem("apikey");
$('#user-name').text(sessionStorage.getItem("fname") + " " +sessionStorage.getItem("lname"));

// $('#upload-dataset').attr("disabled", true);


    $.ajax({url: `./server/api/get-datasets.php?apikey=${apikey}`, 
    method: 'GET',
    success: function(data) {
        json=JSON.parse(data)
        public_datasets=json.public_datasets;
        personal_datasets=json.personal_datasets;
        
        $.each(public_datasets, function (i, item) {
            $('#select-dataset').append($(`<option id="public" value=${item}>(public)${item}</option>`
                
            ));
        });
        $.each(personal_datasets, function (i, item) {
            $('#select-dataset').append($(`<option id="personal" value=${item}>${item}</option>`
                
            ));
        });
        },
    error:function(xhr, status, error) {
        const response=JSON.parse(xhr.responseText)
        $('#error-text-big').text(response.errormesg);
        $('.error-message-big').show();
      }
    })  


    var tableLength;

    // function read_dataset(){
    //   $('.bootstrap-table').remove();
    //     $('.numerical-col-check').remove();
    //     $('#get-elbow-btn').prop('disabled', false);
    //     $('#loading-spinner-elbow').hide();
    //     $('#error-text-columns').text("");
    //     $('.error-columns').hide();
    //     $('#elbow-clusters').val("");
    //     $('.elbow-chart').hide();
    //     $('#error-text-assigment').text("");
    //     $('.error-assigment').hide();
    //     $('#loading-spinner-assigment').hide();
    //     $('#download-clusters-btn').hide();
    //     $('#show-plot-btn').hide();
    //     $('#assigment-clusters').val("");
    //     $('#loading-spinner-plot').show();
    //     $('.imgbox').remove();
    //     $('#download-graph-btn').hide();

    //     let id = $('#select-dataset :selected').attr("id");
    //     if(id==="default-option"){
    //       $('.cont-dataset-table').hide();
    //       $('.initialize-elbow').hide();
    //       $('.clusters-card').hide();
    //       $('#download-btn').addClass("disabled")
    //       $('.delete-btn').prop('disabled',true);
    //       return;
    //     }
    //     $('#download-btn').removeClass("disabled")
    //       $('.delete-btn').prop('disabled',false);
    //     let dataset=$('#select-dataset :selected').val()
    //     let algorithm=$('#select-algorithm :selected').val()
    //     table=`<table class="table table-striped" id="dataset-table" data-show-pagination-switch="false" 
    //     data-pagination="true" data-virtual-scroll="true"> 
    //     <thead> 
    //        <tr>
    //        </tr>
    //      </thead>
    //      </table>
    //      `

    //     $('#loading-spinner-table').show();

    // $.ajax({url: `./server/api/read-dataset.php?dataset=${dataset}&dataset-type=${id.toLowerCase()}&algorithm=${algorithm.toLowerCase()}&apikey=${apikey}`, 
    //   method: 'GET',
    //   dataType: 'json',
    //   success: function(data) {
    //     tableLength=Object.keys(data.items).length;
    //     console.log(tableLength)
    //    $('.cont-dataset-table>.card>.card-body').append(table);
    //    $('.cont-dataset-table').show();
    //    keys=Object.keys(data.items[0]);
    //    $.each(keys,(i,key)=>{
    //     str = key.replace(/\s+/g, '');
    //     $('#dataset-table>thead>tr').append(`<th data-field=${key}>${str}</th>`)
    //   })
    //    $('#dataset-table').bootstrapTable({
    //     data:data['items'],
    //     reinit: true,
    //     // height:500
    //    })
    //    $('#loading-spinner-table').hide();
    //    numerical_cols=data.numerical_columns;
    //    console.log(numerical_cols);
    //    $.each(numerical_cols,(i,element)=>{
    //     $('.column-options').append(`<div class="form-check form-check-inline numerical-col-check">
    //     <input class="form-check-input" type="checkbox" name="numerical-col" value=${element} checked>
    //     <label class="form-check-label" for="inlineCheckbox1">${element}</label>
    //   </div>`)
    //    })
    //    $('.initialize-elbow').show();
    //    $('.clusters-card').show();
    //   },
    //   error:function(xhr, status, error) {
    //     console.log(xhr.responseText);
    //     $('#loading-spinner-table').hide();

    //   }
    // })
    // }
    var numerical_columns;
    var categorical_columns;
    function read_dataset(){
      $('.bootstrap-table').remove();
        $('.numerical-col-check').remove();
        $('#get-elbow-btn').prop('disabled', false);
        $('#loading-spinner-elbow').hide();
        $('#error-text-columns').text("");
        $('#sample-hint-error').hide();
          $('#sample-hint-text').text("")
        $('.error-columns').hide();
        $('#elbow-clusters').val("");
        $('.elbow-chart').hide();
        $('#error-text-assigment').text("");
        $('.error-assigment').hide();
        $('#loading-spinner-assigment').hide();
        $('#download-clusters-btn').hide();
        $('#show-plot-btn').hide();
        $('#show-plot-btn2').remove();
        $('#assigment-clusters').val("");
        $('#loading-spinner-plot').show();
        $('.imgbox').remove();
        $('.imgbox2').remove();
  
        let id = $('#select-dataset :selected').attr("id");
        if(id==="default-option"){
          $('.cont-dataset-table').hide();
          $('.initialize-elbow').hide();
          $('.clusters-card').hide();
          $('#download-btn').addClass("disabled")
          $('.delete-btn').prop('disabled',true);
          return;
        }
  
        
        $('#download-btn').removeClass("disabled")
          $('.delete-btn').prop('disabled',false);
        let dataset=$('#select-dataset :selected').val()
        let algorithm=$('#select-algorithm :selected').val()
        table=`<table class="table table-striped" id="dataset-table"  
        data-pagination="true" data-virtual-scroll="true"> 
        <thead> 
           <tr>
           </tr>
         </thead>
         <tbody>
         </tobody>
         </table>
         `
  
        $('#loading-spinner-table').show();
  
    $.ajax({url: `./server/api/read-dataset.php?dataset=${dataset}&dataset-type=${id.toLowerCase()}&algorithm=${algorithm.toLowerCase()}&apikey=${apikey}`, 
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        tableLength=Object.keys(data.items).length;
        if((tableLength>=1800) && (algorithm.toLowerCase()=="k-prototypes"||algorithm.toLowerCase()=="auto")){
          $('#sample-hint-error').show();
          $('#sample-hint-text').text("Note: When using the Elbow method with the K-Prototypes algorithm, the algorithm operates with a sample dataset size of 1000. Be mindful of this while interpreting results and assessing their relevance to your particular dataset.");
        }
       $('.cont-dataset-table>.card>.card-body').append(table);
       $('.cont-dataset-table').show();
       keys=Object.keys(data.items[0]);
       $.each(keys,(i,key)=>{
        $('#dataset-table>thead>tr').append(`<th data-field=${key}>${key}</th>`)
      })
      
      
      $('#dataset-table').bootstrapTable({
            data:data['items'],
            reinit: true,
           })
       $('#loading-spinner-table').hide();
       numerical_cols=data.available_columns;
       numerical_columns=data.numerical_columns;
       categorical_columns=data.categorical_columns
       $.each(numerical_cols,(i,element)=>{
        $('.column-options').append(`<div class="form-check form-check-inline numerical-col-check">
        <input class="form-check-input" type="checkbox" name="numerical-col" value=${element} checked>
        <label class="form-check-label" for="inlineCheckbox1">${element}</label>
      </div>`)
       })
       $('.initialize-elbow').show();
       $('.clusters-card').show();
      },
      error:function(xhr, status, error) {
        $('#loading-spinner-table').hide();
  
      }
    })
    }

    $('#select-dataset').on('change', ()=> {
        read_dataset();
    })

    $('#select-algorithm').on('change', ()=> {
      
      let id = $('#select-dataset :selected').attr("id");
      if(!(id==="default-option")){
        read_dataset();
      }
      
  })

      
      $(document).on('change', '.file-input', function() {
        $('#error-text-big').text("");
        $('.error-message-big').hide();
        $('#success-text-big').text("");
        $('.success-message-big').hide();
        var allowedExtensions = /(\.csv|\.xlsx|\.xls)$/i;
        const fileToUpload=$('.file-input')[0];

        file=Object.values(fileToUpload.files)[0];

        if(!allowedExtensions.exec(file.name)){
        $('#error-text-big').text("Allowed extension are csv,xlsx,xls");
        $('.error-message-big').show();
        $('.file-input').val('');
            return;
        }

        $('#upload-btn').prop('disabled', false);
        var filesCount = $(this)[0].files.length;
        
        var textbox = $('.file-message');
      
        if (filesCount === 1) {
          var fileName = $(this).val().split('\\').pop();
          textbox.text(fileName);
        } else {
          textbox.text(filesCount + ' files selected');
        }
      });

      $('#upload-btn').click(()=>{
        
        const fileToUpload=$('.file-input')[0];

        let dataset_type=$('#select-folder :selected').val();

        if(!(dataset_type=="Personal"||dataset_type=="Public")){
          $('#error-text-big').text('You must select folder');
          $('.error-message-big').show();
          return;
        }else{
          $('#error-text-big').text('');
          $('.error-message-big').hide();
          
        }

        file=Object.values(fileToUpload.files)[0];


        form_data=new FormData();
        form_data.set("dataset",file);
        form_data.set('apikey',apikey);
        form_data.set("dataset-type",dataset_type.toLowerCase());
       
        $('#loading-spinner-upload').show();

        $('#upload-btn').prop('disabled', true);
        $.ajax({url: "./server/api/upload-dataset.php", 
    method: 'POST',
    data:form_data,
    processData: false,
    contentType: false,
    success: function(data) {
        $('#loading-spinner-upload').hide();
        $('#success-text-big').text("File uploaded successfully");
        $('.success-message-big').show();
        
        if(dataset_type==="Public"){
          $('#select-dataset').append($(`<option id="public" value=${file.name}>(public)${file.name}</option>`));
        }else if(dataset_type==="Personal"){
          $('#select-dataset').append($(`<option id="personal" value=${file.name}>${file.name}</option>`));
        }
        $('.file-input').val('');
        var textbox = $('.file-message');
        textbox.text('or drag and drop files here');
        
      },
    error:function(xhr, status, error) {
        const response=JSON.parse(xhr.responseText)
        $('#error-text-big').text(response.errormesg);
        $('.error-message-big').show();
        $('#loading-spinner-upload').hide();
        $('.file-input').val('');
        var textbox = $('.file-message');
        textbox.text('or drag and drop files here');
        
      }
    })
    })
    $('#download-btn').click((e)=>{
        if($('#download-btn').hasClass("disabled")){
          e.preventDefault();
          return;
        }
        var dataset = $('#select-dataset').children(":selected").val();
        var type= $('#select-dataset').children(":selected").attr("id");
        e.preventDefault();
        window.location.href = `./server/api/download-dataset.php?dataset=${dataset}&dataset-type=${type}&apikey=${apikey}`;
    })

    $('.delete-btn').click(()=>{
        var dataset = $('#select-dataset').children(":selected").val();
        var type= $('#select-dataset').children(":selected").attr("id");

        $('.delete-btn').prop('disabled', true);
        $.ajax({url: "./server/api/delete-dataset.php", 
    method: 'DELETE',
    data:JSON.stringify({dataset:dataset,"dataset-type":type,apikey:apikey}),
    success: function() {
        $('#select-dataset :selected').remove();
        $('.delete-btn').prop('disabled', false);
        $('.cont-dataset-table').hide();
          $('.initialize-elbow').hide();
          $('.clusters-card').hide();
          $('#download-btn').addClass("disabled")
          $('.delete-btn').prop('disabled',true);
    },
    error:function(xhr, status, error) {
        const response=JSON.parse(xhr.responseText)
        $('#error-text').text(response.errormesg);
        $('.error-message').show();
        $('.delete-btn').prop('disabled', false);
        setTimeout(function() { 
            $('#error-text').text("");
            $('.error-message').hide();
        }, 4000);
      }
    })
    })
    $('#upload-dataset').on('hidden.bs.modal', function () {
      $('#error-text-big').text("");
      $('.error-message-big').hide();
      $('#loading-spinner-upload').hide();
      $('.file-input').val("");
      $('#success-text-big').text("");
      $('.success-message-big').hide();
      $('.file-message').text("or drag and drop files here");
    })

    $('#get-elbow-btn').click(()=>{
      $('.elbow-chart').hide();
      $('.elbow-chart>.card-body>.chart-div').remove();
      $('#error-text-elbow').text("");
      $('.error-elbow').hide();
      $('#error-text-columns').text("");
        $('.error-columns').hide();
      numberOfClusters=$('#elbow-clusters').val();
      if(!(numberOfClusters>3)){
        $('.hint-text').css('color','red');
        return;
      }else{
        $('.hint-text').css('color','#A9A9A9');
      }

      if(numberOfClusters>100){
        $('#error-text-elbow').text("The maximum number of clusters is 100");
        $('.error-elbow').show();
        return;
      }

      if(numberOfClusters>tableLength){
        $('#error-text-elbow').text("The number of clusters must be less than or equal to the number of rows of the dataset");
        $('.error-elbow').show();
        return;
      }

      
      let boxes = $('input[name=numerical-col]:checked');
      if(boxes.length==0){
        $('#error-text-columns').text("You must select columns");
        $('.error-columns').show();
        return;
      }
      
      let cols = [];
        boxes.each(function(i){
          cols[i] = $(this).val();
        });
      
      

      let dataset=$('#select-dataset :selected').val();
      let dataset_type=$('#select-dataset :selected').attr("id");

      $('#loading-spinner-elbow').show();
      $('#get-elbow-btn').prop('disabled', true);


      let algorithm=$('#select-algorithm :selected').val().toLowerCase();
     
      
      
      $.ajax({url: `./server/api/elbow.php`, 
      method: 'POST',
      data:JSON.stringify({dataset:dataset,"dataset-type":dataset_type,clusters:numberOfClusters,columns:cols,algorithm:algorithm,apikey:apikey}),
      dataType: 'json',
      success: function(data) {
          $('#loading-spinner-elbow').hide();
          var xValues = Array.from({length:data.sse.length},(v,k)=>k+1)
          canvas=`<div class="chart-div"><canvas class="chart" id="myChart"></canvas>
      <p class="mt-3">Suggested k: ${data['suggested-k']}</p></div>`;
      $('.elbow-chart>.card-body').append(canvas);
new Chart("myChart",{
  type:"line",
  data:{
    labels:xValues,
    datasets: [{
      label:"SSE",
      lineTension: 0,
      borderWidth:'3',
      borderColor: '#4070f4',
      tension: 0.1,
      data: data.sse,
      fill:false
    }]
  },
  options: {
    legend: {display: false},
    scales: {
      yAxes: [{ticks: {min: data.sse[0], max:data.sse[data.sse.length - 1]}}],
    }
  }
  }
)
$('.elbow-chart').show();  
          },
      error:function(xhr, status, error) {
          $('#loading-spinner-elbow').hide();
          $('#get-elbow-btn').prop('disabled', false);
          const response=JSON.parse(xhr.responseText)
          $('#error-text-columns').text(response.errormesg);
          $('.error-columns').show();
        }
      })
    })

    $("#elbow-clusters").on("input", function() {
      $('#get-elbow-btn').prop('disabled', false);
   }); 

  var cols;
   $("#get-assigment-btn").click(()=>{
    $('.bootstrap-table').eq(1).remove();
    $('#error-text-assigment').text("");
    $('.error-assigment').hide();
    $('#error-text-columns').text("");
    $('.error-columns').hide();
    $('#download-clusters-btn').hide();
    $('#show-plot-btn').hide();
    $('#show-plot-btn2').remove();
    $('#loading-spinner-plot').show();
    $('#loading-spinner-plot2').show();
    $('.imgbox').remove();
    $('.imgbox2').remove();

    let clusters=$('#assigment-clusters').val();
    cols=[];

    if(clusters.length==0){
      $('#error-text-assigment').text("Please enter number of clusters");
      $('.error-assigment').show();
      return;
    }

    if(clusters>100){
      $('#error-text-assigment').text("The maximum number of clusters is 100");
      $('.error-assigment').show();
      return;
    }

    if(clusters>tableLength){
      $('#error-text-assigment').text("The number of clusters must be less than or equal to the number of rows of the dataset");
      $('.error-assigment').show();
      return;
    }

    let boxes = $('input[name=numerical-col]:checked');
    if(boxes.length==0){
      $('#error-text-columns').text("You must select columns");
      $('.error-columns').show();
      return;
    }
    
      boxes.each(function(i){
        cols[i] = $(this).val();
      });

      table=`<table class="table table-striped" id="clusters-table" data-show-pagination-switch="false" 
      data-pagination="true" data-virtual-scroll="true"> 
      <thead> 
         <tr>
         </tr>
       </thead>
       </table>
       `

      let dataset=$('#select-dataset :selected').val();
      let dataset_type=$('#select-dataset :selected').attr("id");
      let algorithm=$('#select-algorithm :selected').val().toLowerCase();
      $('#get-assigment-btn').prop('disabled', true);
      $('#loading-spinner-assigment').show();
      
      
 $.ajax({url: `./server/api/clusters.php`, 
    method: 'POST',
    dataType: 'json',
    data:JSON.stringify({dataset:dataset,"dataset-type":dataset_type,clusters:clusters,columns:cols,algorithm:algorithm,apikey:apikey}),
    success: function(data) {

     

      
      if(data.error){
        $('#loading-spinner-assigment').hide();
        $('#error-text-assigment').text(data.error.message);
        $('.error-assigment').show();
      }else{
        $('.cont-clusters-table').append(table);
      $('.cont-clusters-table').show();
      $('#download-clusters-btn').show();
      $('#show-plot-btn').show();
      newbutton=` <button class="p-2 bd-highlight  btn-blue" id="show-plot-btn2"><span>
      <i class="fa-solid fa-chart-line"></i>
      Show plot 2
  </span></button>`
      if(algorithm=="k-prototypes"){
        $('#cluster-buttons').append(newbutton);
      }else if(algorithm=="auto"){
        if(numerical_columns.length>0 && categorical_columns.length>0){
          const containsNumericalData = numerical_cols.some(item => cols.includes(item));
          const containsCategoricalData = categorical_columns.some(item => cols.includes(item));
          if(containsNumericalData&&containsCategoricalData){
            $('#cluster-buttons').append(newbutton);
          }
          
        }
      }
      
      keys=Object.keys(data.items[0]);
      $.each(keys,(i,key)=>{
       $('#clusters-table>thead>tr').append(`<th data-field=${key}>${key}</th>`)
     })
      $('#clusters-table').bootstrapTable({
       data:data.items,
       reinit: true,
       // height:500
      })
      $('#loading-spinner-assigment').hide();
      }
    },
    error:function(xhr, status, error) {
      $('#get-assigment-btn').prop('disabled', false);
      $('#loading-spinner-assigment').hide();
      const response=JSON.parse(xhr.responseText)
          $('#error-text-columns').text(response.errormesg);
          $('.error-columns').show();
      }
    })
   })

   $("#assigment-clusters").on("input", function() {
    $('#get-assigment-btn').prop('disabled', false);
 }); 

 $('#download-clusters-btn').click((e)=>{
  e.preventDefault();
  var dataset = $('#select-dataset').children(":selected").val();
  var type= $('#select-dataset').children(":selected").attr("id");
 let filename = dataset.substring(0,dataset.lastIndexOf('.'));
  let clusters=$('#assigment-clusters').val();
  
  window.location.href = `./server/api/download-dataset.php?dataset=${filename}_clusters_${clusters}.csv&dataset-type=${type}&apikey=${apikey}`;
})



$('#show-plot-btn').click(()=>{
    $('#plotModal').modal('show');
    

    if(!($('.imgbox').length)){
      let dataset=$('#select-dataset :selected').val();
      let dataset_type=$('#select-dataset :selected').attr("id");
        let clusters=$('#assigment-clusters').val();
        let columns=cols;
        let algorithm=$('#select-algorithm :selected').val().toLowerCase();
        if(algorithm=="k-prototypes"||algorithm=="auto"){
          columns=[];
          columns = numerical_columns.filter(column => cols.includes(column));
        }
      $.ajax({url: "./server/api/parallel-cords.php", 
      method: 'POST',
      dataType: 'json',
      data:JSON.stringify({dataset:dataset,"dataset-type":dataset_type,clusters:clusters,columns:columns,apikey:apikey}),
      success: function(data) {
        const extension = data.file.split('.').pop().toLowerCase();
        let content;
        if(extension=="png"){
           content=`<div class="imgbox">
          <img class="center-fit" src='${data.file}'>
      </div>`
      $('.plot-body').append(content);
      $('#loading-spinner-plot').hide();
        }else{
           content=`<div class="imgbox">
          <iframe id="plotframe" src=${data.file} width=100% height=100%></iframe>
      </div>`
      $('.plot-body').append(content);
      $('#plotframe').on('load', function() {
        $('#loading-spinner-plot').hide();
      })
        }
       
       
       
      },
      error:function(xhr, status, error) {
        console.log(JSON.parse(xhr.responseText))
        }
      })
    }

})






$(document).on('click', '#show-plot-btn2', function(){
  $('#plotModal2').modal('show');
    

    if(!($('.imgbox2').length)){
      let dataset=$('#select-dataset :selected').val();
      let dataset_type=$('#select-dataset :selected').attr("id");
        let clusters=$('#assigment-clusters').val();
      $.ajax({url: "./server/api/parallel-cords.php", 
      method: 'POST',
      dataType: 'json',
      data:JSON.stringify({dataset:dataset,"dataset-type":dataset_type,clusters:clusters,columns:cols,apikey:apikey}),
      success: function(data) {
        const extension = data.file.split('.').pop().toLowerCase();
        let content;
        if(extension=="png"){
           content=`<div class="imgbox">
          <img class="center-fit" src='${data.file}'>
      </div>`
      $('.plot-body2').append(content);
      $('#loading-spinner-plot2').hide();
        }else{
           content=`<div class="imgbox2">
          <iframe id="plotframe2" src=${data.file} width=100% height=100%></iframe>
      </div>`
      $('.plot-body2').append(content);
      $('#plotframe2').on('load', function() {
        $('#loading-spinner-plot2').hide();
      })
        }
       
       
       
      },
      error:function(xhr, status, error) {
        console.log(JSON.parse(xhr.responseText))
        }
      })
    }

})

$('#sign-out-btn').click(()=>{
  sessionStorage.clear();
  window.location.href = "./";
})

function applyModalBehavior($element) {
  $element.find('.modal-content')
    .css({
      width: 800,
      height: 650,
    })
    .resizable({
      aspectRatio: true,
      minWidth: 800,
      minHeight: 650,
      handles: 'n, e, s, w, ne, sw, se, nw',
    })
    .draggable({
      handle: '.modal-header'
    });
}

var $plotModal = $('#plotModal');
var $plotModal2 = $('#plotModal2');

applyModalBehavior($plotModal);
applyModalBehavior($plotModal2);

     


});

        


    


