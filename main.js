let maxLevelId = $('#maxLevelId').val();
// console.log(maxLevelId);


let increaseMaxLevelId = (mli) => {
    mli= parseInt(mli.split('-')[1]);
    mli++;
    maxLevelId = 'no-'+mli;
    $('#maxLevelId').val(maxLevelId);
    return maxLevelId;
}

$(function() {
  let treeview = {
    resetBtnToggle: function() {
      $(".js-treeview")
        .find(".level-add")
        .find("span")
        .removeClass()
        .addClass("fa fa-plus");
      $(".js-treeview")
        .find(".level-add")
        .siblings()
        .removeClass("in");
    },
    addSameLevel: function(target, iml) {
      let ulElm = target.closest("ul");
      // let sameLevelCodeASCII = target
      //   .closest("[data-level]")
      //   .attr("data-level")
      //   .charCodeAt(0);
      let dataLevel = parseInt(target
        .closest("[data-level]")
        .attr("data-level"));
      console.log(dataLevel);
      ulElm.append($("#levelMarkup").html());
      ulElm
        .children("li:last-child")
        .find("[data-level]")
        .attr({
            // "data-level": String.fromCharCode(sameLevelCodeASCII),
            "data-level": dataLevel,
            "data-id-abstract": iml,
            "id": iml
        });
      // let levelTitle = 'Level ' + String.fromCharCode(sameLevelCodeASCII);
      let levelTitle = 'Level ' + dataLevel;
      ulElm
        .children("li:last-child")
        .find("span.level-title")[0].innerHTML = levelTitle;
      
      let parent_id = parseInt(target
        .closest("[data-parent_id]")
        .attr("data-parent_id"));

      $('input#c_abstract_id').val(iml);
      $('input#c_parent_id').val(parent_id);
      $('input#c_name').val(levelTitle);
      $("form#createForm").submit();
    },

    addSubLevel: function(target, iml) {
      let liElm = target.closest("li");
      // let nextLevelCodeASCII = liElm.find("[data-level]").attr("data-level").charCodeAt(0) + 1;
      let dataLevel = parseInt(liElm.find("[data-level]").attr("data-level"))+1;

      liElm.children("ul").append($("#levelMarkup").html());
      liElm.children("ul")
        .children("li:last-child")
        .find("[data-level]")
        .attr({
            // "data-level": String.fromCharCode(nextLevelCodeASCII),
            "data-level": dataLevel,
            "data-id-abstract": iml,
            'id': iml
        });

      let levelTitle = 'Level ' + dataLevel;
      liElm.children("ul")
        .find("span.level-title").last()[0].innerHTML = levelTitle;
      
      let parent_id = parseInt(target
        .closest("[data-id-real]")
        .attr("data-id-real"));

      $('input#c_abstract_id').val(iml);
      $('input#c_parent_id').val(parent_id)
      $('input#c_name').val(levelTitle);
      $("form#createForm").submit();
    },
    removeLevel: function(target) {
      target.closest("li").remove();
      
    },
  };

  // Treeview Functions
  $(".js-treeview").on("click", ".level-add", function() {
    // treeview.resetBtnToggle();
    $(this).find("span").toggleClass("fa-plus").toggleClass("fa-times text-danger");
    $(this).closest(".js-treeview").find("[data-level]").removeClass("selected");
    $(this).siblings().toggleClass("in");
  });

  // Add same level
  $(".js-treeview").on("click", ".level-same", function() {
    maxLevelId = $('#maxLevelId').val();
    let iml = increaseMaxLevelId(maxLevelId);
    treeview.addSameLevel($(this), iml);
    treeview.resetBtnToggle();
  });

  // Add sub level
  $(".js-treeview").on("click", ".level-sub", function(e) {
    event.preventDefault();
    maxLevelId = $('#maxLevelId').val();
    let iml = increaseMaxLevelId(maxLevelId);
    treeview.addSubLevel($(this), iml);
    treeview.resetBtnToggle();
  });

  // Remove Level
  $(".js-treeview").on("click", ".level-remove", function(event) {
    let id = event.currentTarget.parentElement.getAttribute("data-id-real");
    $("#d_id").val(id);
    $("form#deleteForm").submit();
    treeview.removeLevel($(this));
  });
  
  // Rename Level
  $(".js-treeview").on("click", ".level-rename", function() {
        let isSelected = $(this).closest("[data-level]").hasClass("selected");
        !isSelected && $(this).closest(".js-treeview").find("[data-level]").removeClass("selected");
        $(this).closest("[data-level]").toggleClass("selected");
        treeview.resetBtnToggle();
  });

  // Selected Level
  $(".js-treeview").on("click", ".level-title", function() {
    // console.log($("[data-level=A]"));
    treeview.resetBtnToggle();
    // console.log($(this).closest("[data-level]"));
    let isSelected = $(this).closest("[data-level]").hasClass("selected");
    !isSelected && $(this).closest(".js-treeview").find("[data-level]").removeClass("selected");
    $(this).closest("[data-level]").toggleClass("selected");
    // $(this)[0].parentElement.children[1].children[4].classList.toggle("in-rename");
  }); 
});


$('#myModal').on('show.bs.modal', function (event) {
    let triggerElement = $(event.relatedTarget); 
    let renameTarget = triggerElement[0].parentElement.previousElementSibling;
    let renameTargetId = renameTarget.nextElementSibling.getAttribute('data-id-real');
    let renameTargetIdAbstract = renameTarget.parentElement.getAttribute("data-id-abstract");
    
    $("#modalLabel").html("Rename Level "+renameTarget.parentElement.getAttribute('data-level')+" Name");
    $("label#labelInputModal").html("New Level "+renameTarget.parentElement.getAttribute('data-level')+" Name:");
    $("#renameModalButton").attr("data-id-abstract", renameTargetIdAbstract);

    $("input#idUpdate").val(parseInt(renameTargetId));
    $("input#nameUpdate").val(renameTarget.innerText);
})


$("form#deleteForm").submit(function (event) {
  event.preventDefault();
  $.ajax({
      type: "POST",
      url: "d_action.php",
      data: $(this).serialize(),
      dataType: 'JSON',
      success: function (data) {
        console.log(data.id);
        console.log("Berhasil delete lur!!!");
      }
  });
});


$("form#updateForm").submit(function (event) {
  event.preventDefault();
  $.ajax({
      type: "POST",
      url: "u_action.php",
      data: $(this).serialize(),
      dataType: 'JSON',
      success: function (data) {
        console.log(data.id);
        console.log("Berhasil update lur!!!");
      }
  });
});

$("form#createForm").submit(function (event) {
  event.preventDefault();
  $.ajax({
      type: "POST",
      url: "c_action.php",
      data: $(this).serialize(),
      dataType: 'JSON',
      success: function (data) {
        // console.log(data.id);
        // console.log(data.parent_id);
        // console.log(data.name);
        // console.log(event.currentTarget.abstract_id.value);
        let abstract_id = event.currentTarget.abstract_id.value;
        let id = data.id;
        let parent_id = data.parent_id; 
        $("#"+abstract_id).find('div.treeview__level-btns').attr('data-id-real', id);
        $("#"+abstract_id).find('div.treeview__level-btns').attr('data-parent_id', parent_id);
        console.log($("#"+abstract_id).find('div.treeview__level-btns').attr('data-id-real'));
        console.log($("#"+abstract_id).find('div.treeview__level-btns').attr('data-parent_id'));
      }
  });
});

$('#renameModalButton').on('click', function () {
  let new_value = $("input#nameUpdate").val();
  let renameTargetIdAbstract = $("#renameModalButton").attr("data-id-abstract");
  $("#"+renameTargetIdAbstract)[0].children[0].innerHTML = new_value;
  $("form#updateForm").submit();
  $('#myModal').modal('hide');
})