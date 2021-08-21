$(document).ready(function() {
  const apsisGrid = new window.prestashop.component.Grid(window.apsisGridId);
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.BulkActionCheckboxExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.ChoiceExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.ExportToSqlManagerExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersResetExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.FiltersSubmitButtonEnablerExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.LinkRowActionExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.ReloadListExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.SortingExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitBulkActionExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitGridActionExtension());
  apsisGrid.addExtension(new window.prestashop.component.GridExtensions.SubmitRowActionExtension());
});
