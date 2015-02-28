function submitbutton(pressbutton) {
  var result = confirm("Сбросить результаты проверки?");
  if(result == true) {
    switch(pressbutton) {
      case 'resetall':
        window.location.href = 'index.php?option=com_sharechecker&task=resetall';
        break;
      case 'resetbroken':
        window.location.href = 'index.php?option=com_sharechecker&task=resetbroken';
        break;
    }
  }
}