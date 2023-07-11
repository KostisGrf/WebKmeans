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
        console.log(json.personal_datasets);
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

    $('#select-dataset').on('change', ()=> {
        $('.bootstrap-table').remove();
        $('.numerical-col-check').remove();
        $('#get-elbow-btn').prop('disabled', false);
        $('#loading-spinner-elbow').hide();
        $('#error-text-columns').text("");
        $('.error-columns').hide();
        $('#elbow-clusters').val("");
        $('.elbow-chart').hide();
        $('#error-text-assigment').text("");
        $('.error-assigment').hide();
        $('#loading-spinner-assigment').hide();
        $('#download-clusters-btn').hide();
<<<<<<< HEAD
        $('#show-plot-btn').hide();
        $('#assigment-clusters').val("");
        $('#loading-spinner-plot').show();
        $('.imgbox').remove();
        $('#download-graph-btn').hide();
=======
        $('#assigment-clusters').val("");
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f

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
        
        table=`<table class="table table-striped" id="dataset-table" data-show-pagination-switch="false" 
        data-pagination="true" data-virtual-scroll="true"> 
        <thead> 
           <tr>
           </tr>
         </thead>
         </table>
         `

        $('#loading-spinner-table').show();

    $.ajax({url: `./server/api/read-dataset.php?dataset=${dataset}&dataset-type=${id.toLowerCase()}&apikey=${apikey}`, 
      method: 'GET',
      dataType: 'json',
      success: function(data) {
        tableLength=Object.keys(data.items).length;
        console.log(tableLength)
       $('.cont-dataset-table>.card>.card-body').append(table);
       $('.cont-dataset-table').show();
       keys=Object.keys(data.items[0]);
       $.each(keys,(i,key)=>{
        $('#dataset-table>thead>tr').append(`<th data-field=${key}>${key}</th>`)
      })
       $('#dataset-table').bootstrapTable({
        data:data.items,
        reinit: true,
        // height:500
       })
       $('#loading-spinner-table').hide();
       numerical_cols=data.numerical_columns;
       console.log(numerical_cols);
       $.each(numerical_cols,(i,element)=>{
        $('.column-options').append(`<div class="form-check form-check-inline numerical-col-check">
<<<<<<< HEAD
        <input class="form-check-input" type="checkbox" name="numerical-col" value=${element} checked>
=======
        <input class="form-check-input" type="checkbox" name="numerical-col" value=${element}>
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f
        <label class="form-check-label" for="inlineCheckbox1">${element}</label>
      </div>`)
       })
       $('.initialize-elbow').show();
       $('.clusters-card').show();
      },
      error:function(xhr, status, error) {
        console.log(xhr.responseText);
        $('#loading-spinner-table').hide();

      }
    })
    })
      
      $(document).on('change', '.file-input', function() {
        $('#error-text-big').text("");
        $('.error-message-big').hide();
        $('#success-text-big').text("");
        $('.success-message-big').hide();
        var allowedExtensions = /(\.csv|\.xlsx|\.xls)$/i;
        const fileToUpload=$('.file-input')[0];

        file=Object.values(fileToUpload.files)[0];

        console.log(file.name);
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
        console.log("folder:"+typeof dataset_type)

        if(!(dataset_type=="Personal"||dataset_type=="Public")){
          $('#error-text-big').text('You must select folder');
          $('.error-message-big').show();
          return;
        }else{
          $('#error-text-big').text('');
          $('.error-message-big').hide();
          
        }

        file=Object.values(fileToUpload.files)[0];

        console.log(file.name);

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
        console.log("number of cluster must be bigger than 3");
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
      
      
      $.ajax({url: "./server/api/elbow.php", 
      method: 'POST',
      data:JSON.stringify({dataset:dataset,"dataset-type":dataset_type,clusters:numberOfClusters,columns:cols,apikey:apikey}),
      dataType: 'json',
      success: function(data) {
        console.log(data.sse.length)
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
          console.log(xhr.responseText);
          $('#loading-spinner-elbow').hide();
          $('#get-elbow-btn').prop('disabled', false);
        }
      })
    })

    $("#elbow-clusters").on("input", function() {
      $('#get-elbow-btn').prop('disabled', false);
   }); 


   $("#get-assigment-btn").click(()=>{
    $('.bootstrap-table').eq(1).remove();
    $('#error-text-assigment').text("");
    $('.error-assigment').hide();
    $('#error-text-columns').text("");
    $('.error-columns').hide();
    $('#download-clusters-btn').hide();
<<<<<<< HEAD
    $('#show-plot-btn').hide();
    $('#loading-spinner-plot').show();
    $('.imgbox').remove();
    $('#download-graph-btn').hide();
=======
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f

    let clusters=$('#assigment-clusters').val();

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
    
    let cols = [];
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
      $('#get-assigment-btn').prop('disabled', true);
      $('#loading-spinner-assigment').show();
      
 $.ajax({url: "./server/api/clusters.php", 
    method: 'POST',
    dataType: 'json',
    data:JSON.stringify({dataset:dataset,"dataset-type":dataset_type,clusters:clusters,columns:cols,apikey:apikey}),
    success: function(data) {
      $('.cont-clusters-table').append(table);
      $('.cont-clusters-table').show();
      $('#download-clusters-btn').show();
<<<<<<< HEAD
      $('#show-plot-btn').show();
=======
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f
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
      
    },
    error:function(xhr, status, error) {
      $('#get-assigment-btn').prop('disabled', false);
      $('#loading-spinner-assigment').hide();
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

<<<<<<< HEAD

var plotGraphImage;
$('#show-plot-btn').click(()=>{
    $('#plotModal').modal('show');

    if(!($('.imgbox').length)){
      let dataset=$('#select-dataset :selected').val();
      let dataset_type=$('#select-dataset :selected').attr("id");
      let cols = [];
      let boxes = $('input[name=numerical-col]:checked');
        boxes.each(function(i){
          cols[i] = $(this).val();
        });
        let clusters=$('#assigment-clusters').val();
  
      $.ajax({url: "./server/api/parallel-cords.php", 
      method: 'POST',
      dataType: 'json',
      data:JSON.stringify({dataset:dataset,"dataset-type":dataset_type,clusters:clusters,columns:cols,apikey:apikey}),
      success: function(data) {
        let plot_image=`<div class="imgbox">
        <img class="center-fit" src='${data.image}'>
    </div>`
        $('#loading-spinner-plot').hide();
        $('.plot-body').append(plot_image);
        $('#download-graph-btn').show();
        plotGraphImage=data.image;

        
      },
      error:function(xhr, status, error) {
        console.log(JSON.parse(xhr.responseText))
        }
      })
    }

  $('#download-graph-btn').click(()=>{
    let dataset=$('#select-dataset :selected').val();
    var lastDotPosition = dataset.lastIndexOf('.');
    var datasetName = dataset.substring(0, lastDotPosition);
    let clusters=$('#assigment-clusters').val();
    imageName=`${datasetName}_clusters_${clusters}`
    var link = document.createElement('a');
    link.href = plotGraphImage;
    link.download = `${imageName}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  })
    


})

=======
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f
$('#sign-out-btn').click(()=>{
  sessionStorage.clear();
  window.location.href = "./";
})

<<<<<<< HEAD
var $plotModal = $('#plotModal');
$plotModal.find('.modal-content')
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

     


});
=======

})
>>>>>>> 549c676594f3b31b1fbceaece92a9e19c635fc8f

        


    


