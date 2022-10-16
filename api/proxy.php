<?php
$json = file_get_contents('php://input');

$data = json_decode($json);
//Data, connection, auth
//~ $dataFromTheForm = $_POST['fieldName']; // request data from the form
$soapUrl = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService'; // asmx URL of WSDL
//~ $zipb64 = 'UEsDBAoAAAAAAPQcAlWkE1ZZtCIAALQiAAAbAAAAMjA2MDY4MjkyNjUtMDMtQjAwMC0xODEueG1sPEludm9pY2UgeG1sbnM9InVybjpvYXNpczpuYW1lczpzcGVjaWZpY2F0aW9uOnVibDpzY2hlbWE6eHNkOkludm9pY2UtMiIgeG1sbnM6Y2FjPSJ1cm46b2FzaXM6bmFtZXM6c3BlY2lmaWNhdGlvbjp1Ymw6c2NoZW1hOnhzZDpDb21tb25BZ2dyZWdhdGVDb21wb25lbnRzLTIiIHhtbG5zOmNiYz0idXJuOm9hc2lzOm5hbWVzOnNwZWNpZmljYXRpb246dWJsOnNjaGVtYTp4c2Q6Q29tbW9uQmFzaWNDb21wb25lbnRzLTIiIHhtbG5zOmRzPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjIiB4bWxuczpleHQ9InVybjpvYXNpczpuYW1lczpzcGVjaWZpY2F0aW9uOnVibDpzY2hlbWE6eHNkOkNvbW1vbkV4dGVuc2lvbkNvbXBvbmVudHMtMiI+PGV4dDpVQkxFeHRlbnNpb25zPjxleHQ6VUJMRXh0ZW5zaW9uPjxleHQ6RXh0ZW5zaW9uQ29udGVudD48ZHM6U2lnbmF0dXJlIElkPSJpZC1jMzdlMGY3YzUyNDkiPjxkczpTaWduZWRJbmZvPjxkczpDYW5vbmljYWxpemF0aW9uTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvVFIvMjAwMS9SRUMteG1sLWMxNG4tMjAwMTAzMTUiLz48ZHM6U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8wNC94bWxkc2lnLW1vcmUjcnNhLXNoYTI1NiIvPjxkczpSZWZlcmVuY2UgVVJJPSIiPjxkczpUcmFuc2Zvcm1zPjxkczpUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwLzA5L3htbGRzaWcjZW52ZWxvcGVkLXNpZ25hdHVyZSIvPjxkczpUcmFuc2Zvcm0gQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy9UUi8yMDAxL1JFQy14bWwtYzE0bi0yMDAxMDMxNSIvPjwvZHM6VHJhbnNmb3Jtcz48ZHM6RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8wNC94bWxlbmMjc2hhMjU2Ii8+PGRzOkRpZ2VzdFZhbHVlPnFuSGlEK3NWeXkyQi8wWURsZ2tMTm1md0NpcEMyWHlFaVlEU3YybFNaaEk9PC9kczpEaWdlc3RWYWx1ZT48L2RzOlJlZmVyZW5jZT48ZHM6UmVmZXJlbmNlIFVSST0iI3hhZGVzLWlkLWMzN2UwZjdjNTI0OSIgVHlwZT0iaHR0cDovL3VyaS5ldHNpLm9yZy8wMTkwMyNTaWduZWRQcm9wZXJ0aWVzIj48ZHM6RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8wNC94bWxlbmMjc2hhMjU2Ii8+PGRzOkRpZ2VzdFZhbHVlPmxDQ1N6T09QUkk3TzlyUC9QOVludzJsTUVZOXlqQXNValk4MjhHK1NQUHM9PC9kczpEaWdlc3RWYWx1ZT48L2RzOlJlZmVyZW5jZT48L2RzOlNpZ25lZEluZm8+PGRzOlNpZ25hdHVyZVZhbHVlPnBJczIvR3hpMjdaZ0g1MzhlSk52dFdFc2xWWWJqRy91eGNZcXA2cGl6eVk4WUdTaTE0emZneGN4MmNGMm1zQWhrWUtlQ01uZ00rVEE2VTQrZHZWVXhLZVdKcGZkYkZ5M2F1b0hNYXBHTDVRQlAwNU44TFVOcTJMM3dRa2Z4QjBMZjJSc3pVZDc4SnY1ZHJka2k5OTE5Y1h2Z3pkNzJMNDVHMFFpUGdta1Q2OG41NFo4R3VYbWpYVldQMldQZC84dzFkcGkrbjlGanM0dnZCZWVvREJNcjJHaVRUVTZicDlsVlFNeHpoY1JVMDRpRmlLM0RwcDhTSDZFR2JFOEJVbGprcnpkbFNnWTROcnNtRXpTZjJhdDBWaTZnUVZneEVGMWFwTzZ6cDlZMUJNTnhYQnplYk5sVW9hQXJuNnRZdEJBN3VQYzFNRG1pT0VHMmc4RytaUHpXajRDbEZzMWMzMjJFcXNNTkROdE5ud2IrVkN5ek8xR3NGZS82T2ZSK3RZNzBycmZ0NGdtd3cxTlpOaHQ5SVllV1AxaTZtY2FIQWFIdzBxMW5nN0VyckNPakJnWm5hc2NnNWNjMDZOVHZzSDgydGc0TlkzaThjYTIwOWE5RUtsd3Y2Vnp2UVFvNEdmTGQwZU85d0ZScUE1TTZWZ0kzZzFsU1VXZGZWMk5vZ3h1ZkpHWGdoSDIrSHZ4SUtmSXFuN2dvMThUcUptUEExMU8rTE1KN1FmNi9qTGh0Y2xyVVhWR0dWeXRYTXBuMkxONVp5VkFqYzVYK1ZyL2tvWkEvazdpT0ZCY1JEWC9hSGQzWnlIU0VtdjhhMms5em5SNWNteVBSeHlib1VDYmgvWjBrMEVyMXEybUFVTkxpTHptZnlDOHJmSm1yRWJLYXQ3dytmUHllVUdwek1zPTwvZHM6U2lnbmF0dXJlVmFsdWU+PGRzOktleUluZm8+PGRzOlg1MDlEYXRhPjxkczpYNTA5Q2VydGlmaWNhdGU+TUlJRmh6Q0NBM09nQXdJQkFnSVFwY3hVd1o3M2xvQlBISy84TU1Mc25EQUpCZ1VyRGdNQ0hRVUFNRDh4SlRBakJnTlZCQXNUSEVST1NTQTBPREF3TkRnek5TQlNWVU1nTWpBeU1UQTBOREV5TVRNeEZqQVVCZ05WQkFNVERWUkpJRk5QVEZWRFNVOU9SVk13SGhjTk1UWXhNREkyTVRRMU5ERTNXaGNOTXpreE1qTXhNak0xT1RVNVdqQS9NU1V3SXdZRFZRUUxFeHhFVGtrZ05EZ3dNRFE0TXpVZ1VsVkRJREl3TWpFd05EUXhNakV6TVJZd0ZBWURWUVFERXcxVVNTQlRUMHhWUTBsUFRrVlRNSUlDSWpBTkJna3Foa2lHOXcwQkFRRUZBQU9DQWc4QU1JSUNDZ0tDQWdFQXZQbmFMeWdORDdnSmYxdHk1NkVOWUs1NUp5Q3lJKzdhckp1SS9wdUxGcDI1SFg0RTM3RzdkdkxBdTM2dXQ0NVk5by9Wc29BbVJ2M0dWemQvcnZpVjQrdTFnSTJmMjdXMGE3TG1sU1BSS0tSaFpXb2tqTXV4a1hoSnI2UndWVWpobDkrWVFsdm5FTnZ2OWJKRDYxMTkxSXFSZ3FpbURTSmFEejd2OTVEM2oxYnhnL0RxZ2J4UUJ1UnhDMXdWOWRRNkNXdC8wai9nTHF1c0N5MCtxdExIMW42dXNKRzcrZDFaaHFlbGxTWEtVMWhuMmFxeUlleFdaWmxhdVlOMUVyOUZVRkw4a0lreTQ4K1M1elBQK2dxM005N0dvVDFncU1WRHM0amJyTXdaOXZWcXNzRFdOMm5scmR3L1Z3aXc4bm5lanBEd1EzMHk1ODVHQkZHWWJpajVOS1kzV2lwMVpqOG0vVTFPRHNCMk1ldkJMNGhjT0RpSzRoNEFzdFpDQmxJaHA3ckN2ZmY0Ym1BNDRQM09TcmtJcThOSytYVTNROEVYQzkvZUJReEZndzltSDNiejFHZVphbmdYYmN6djFDY1A1S21rTkx0VmdNSkt4dGoxeVZrRlJyU01WRCtpbzdrZUJqOHlqU1JQYzVJWm8rcWJZZEtzNURCZU0rYjZZRmdmQ1EvV1lMQjRaQi8xYVRsTGpIdEZBYnlvYVJGTHlYZDcwWG82UzFNQzg4cEtqdi8vWnpWblE4QTExZDZlTEJJeXdrR2lvS3NkQ3dkWjNuc3AwNWNhQk1hS3ZySmd1Skh6TjdjZllBUVNKQnBFazNmUHBCdFU1eFRMTmV1eFNYc0VGQUJqcE1RYlMxVHRwR1BFN3VkUnhSVStCb2RmekkrM054ZTB0dWxrK2VVQ0F3RUFBYU9CaGpDQmd6QVBCZ05WSFJNQkFmOEVCVEFEQVFIL01IQUdBMVVkQVFScE1HZUFFSmFxbkI4NUFQUVNHcEg1a042U0VCV2hRVEEvTVNVd0l3WURWUVFMRXh4RVRra2dORGd3TURRNE16VWdVbFZESURJd01qRXdORFF4TWpFek1SWXdGQVlEVlFRREV3MVVTU0JUVDB4VlEwbFBUa1ZUZ2hDbHpGVEJudmVXZ0U4Y3Ivd3d3dXljTUFrR0JTc09Bd0lkQlFBRGdnSUJBTGFvbEY2TkVHL0xzL3BXbUFtS1B3QjlFa3hXb1Q1NTMzbWJycG5ieGtvdVBWRUNkZDdHLzhPa2VJMURWSE9HMVJXQ0hPak1pTmw2MWdKNEorTWZUWjluQVpSR1VyUysvNDdwdURLaGxaVXhDLy9XYitjYllaZDZOVHpDb3Z1MXNWOW12MHpNWEdBR01rU2Z1ekxVL1gwOUhMWjU0UGpZbVhwSFJnNlVrNXg4SUNkR0pmeWJrYXJTeVBxTFV4STVtQ0ZnWEJibTB6RkdaVU5yR3ZtZDZoelQ1YkdBYmQ0VEZxUVBDVG02STFMRlB5SERvSHJsNXdFbS85OXczRjBmYmovZm1aUCt2Um5HSEo0Z1pVZExnZFIxZmFrendzSDZPdHNzNTFrd29OaWRVOURmZldvbEZjRzZMbk1wcGZqNVphQXQyVjRaQU8yclNzQkRUckNPNHc0OTVGS0hrT0I1b0R6MmJZTndEbHVta0RpQmxMckcyRmJXdGYzNlRHQkhkOUVIS3NnUW1LTGRacERFbUJmMnFKeFRBN2RSeGVrcGkxOUNaQXVJKzRYZzdDMlp6a1RaQmJqNjRJejhZVWZvTlVPdFVUNzFkRlBCcytIZXF2SG5DUGtGOUZMTmljZ2tNcFRqZ2VzditxVXJmK0hUaFZRUnNpb1F5OVVFNHFnK1NpQzh4dnY5bjF5blpaTkFaSGMrOFc0b2tmdnY3UERKY0YxZU8vZHlXU3c1SndUdUJtZ0I3MG1lVm5sSnE0cDhsSDJWRXMxSmFPSXNrQ0VhaVgwWnEwdXBudldQVjJqd25MMXlsNlNpaDhKM0RMZi9JNkZPNlBIRkdwY3pGRnNXMFZUTTlURU9yWGRkd3NleCtFTEYweXhrR1V4NXpZVC9EQ0c4SFIzdmdNT2k8L2RzOlg1MDlDZXJ0aWZpY2F0ZT48L2RzOlg1MDlEYXRhPjwvZHM6S2V5SW5mbz48ZHM6T2JqZWN0Pjx4YWRlczpRdWFsaWZ5aW5nUHJvcGVydGllcyB4bWxuczp4YWRlcz0iaHR0cDovL3VyaS5ldHNpLm9yZy8wMTkwMy92MS4zLjIjIiBUYXJnZXQ9IiNpZC1jMzdlMGY3YzUyNDkiPjx4YWRlczpTaWduZWRQcm9wZXJ0aWVzIElkPSJ4YWRlcy1pZC1jMzdlMGY3YzUyNDkiPjx4YWRlczpTaWduZWRTaWduYXR1cmVQcm9wZXJ0aWVzPjx4YWRlczpTaWduaW5nVGltZT4yMDIyLTA4LTAyVDAzOjM4OjU1LjI3MFo8L3hhZGVzOlNpZ25pbmdUaW1lPjx4YWRlczpTaWduaW5nQ2VydGlmaWNhdGU+PHhhZGVzOkNlcnQ+PHhhZGVzOkNlcnREaWdlc3Q+PGRzOkRpZ2VzdE1ldGhvZCBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMDQveG1sZW5jI3NoYTI1NiIvPjxkczpEaWdlc3RWYWx1ZT5MNHZFQTRsdU4wbkdDazFqZkM0QTUzOEVqQnRGZnN4ZFlDMmV1NDV2NjRBPTwvZHM6RGlnZXN0VmFsdWU+PC94YWRlczpDZXJ0RGlnZXN0Pjx4YWRlczpJc3N1ZXJTZXJpYWw+PGRzOlg1MDlJc3N1ZXJOYW1lPk9VPUROSSA0ODAwNDgzNSBSVUMgMjAyMTA0NDEyMTMsIENOPVRJIFNPTFVDSU9ORVM8L2RzOlg1MDlJc3N1ZXJOYW1lPjxkczpYNTA5U2VyaWFsTnVtYmVyPi0xMTk4OTg3OTk5OTQ2NDYyMjA2NTM2MTk4MjMyMzg5NTg2MTc0NDQ8L2RzOlg1MDlTZXJpYWxOdW1iZXI+PC94YWRlczpJc3N1ZXJTZXJpYWw+PC94YWRlczpDZXJ0PjwveGFkZXM6U2lnbmluZ0NlcnRpZmljYXRlPjx4YWRlczpTaWduYXR1cmVQb2xpY3lJZGVudGlmaWVyPjx4YWRlczpTaWduYXR1cmVQb2xpY3lJbXBsaWVkLz48L3hhZGVzOlNpZ25hdHVyZVBvbGljeUlkZW50aWZpZXI+PC94YWRlczpTaWduZWRTaWduYXR1cmVQcm9wZXJ0aWVzPjwveGFkZXM6U2lnbmVkUHJvcGVydGllcz48L3hhZGVzOlF1YWxpZnlpbmdQcm9wZXJ0aWVzPjwvZHM6T2JqZWN0PjwvZHM6U2lnbmF0dXJlPjwvZXh0OkV4dGVuc2lvbkNvbnRlbnQ+PC9leHQ6VUJMRXh0ZW5zaW9uPjwvZXh0OlVCTEV4dGVuc2lvbnM+PGNiYzpVQkxWZXJzaW9uSUQ+Mi4xPC9jYmM6VUJMVmVyc2lvbklEPjxjYmM6Q3VzdG9taXphdGlvbklEPjIuMDwvY2JjOkN1c3RvbWl6YXRpb25JRD48Y2JjOklEPkIwMDAtMTgxPC9jYmM6SUQ+PGNiYzpJc3N1ZURhdGU+MjAyMi0wNy0yOTwvY2JjOklzc3VlRGF0ZT48Y2JjOklzc3VlVGltZT4xMTowNDozMDwvY2JjOklzc3VlVGltZT48Y2JjOkludm9pY2VUeXBlQ29kZSBsaXN0SUQ9IjAxMDEiPjAzPC9jYmM6SW52b2ljZVR5cGVDb2RlPjxjYmM6Tm90ZSBsYW5ndWFnZUxvY2FsZUlEPSIxMDAwIj48IVtDREFUQVtVTk8gWSAyOC8xMDBdXT48L2NiYzpOb3RlPjxjYmM6RG9jdW1lbnRDdXJyZW5jeUNvZGU+VVNEPC9jYmM6RG9jdW1lbnRDdXJyZW5jeUNvZGU+PGNhYzpPcmRlclJlZmVyZW5jZT48Y2JjOklEPjcwMjwvY2JjOklEPjwvY2FjOk9yZGVyUmVmZXJlbmNlPjxjYWM6U2lnbmF0dXJlPjxjYmM6SUQ+MjA2MDY4MjkyNjU8L2NiYzpJRD48Y2FjOlNpZ25hdG9yeVBhcnR5PjxjYWM6UGFydHlJZGVudGlmaWNhdGlvbj48Y2JjOklEPjIwNjA2ODI5MjY1PC9jYmM6SUQ+PC9jYWM6UGFydHlJZGVudGlmaWNhdGlvbj48Y2FjOlBhcnR5TmFtZT48Y2JjOk5hbWU+PCFbQ0RBVEFbRUZFQ1RJQklUIFNBQ11dPjwvY2JjOk5hbWU+PC9jYWM6UGFydHlOYW1lPjwvY2FjOlNpZ25hdG9yeVBhcnR5PjxjYWM6RGlnaXRhbFNpZ25hdHVyZUF0dGFjaG1lbnQ+PGNhYzpFeHRlcm5hbFJlZmVyZW5jZT48Y2JjOlVSST4jRUZFQ1RJRklSTUE8L2NiYzpVUkk+PC9jYWM6RXh0ZXJuYWxSZWZlcmVuY2U+PC9jYWM6RGlnaXRhbFNpZ25hdHVyZUF0dGFjaG1lbnQ+PC9jYWM6U2lnbmF0dXJlPjxjYWM6QWNjb3VudGluZ1N1cHBsaWVyUGFydHk+PGNhYzpQYXJ0eT48Y2FjOlBhcnR5SWRlbnRpZmljYXRpb24+PGNiYzpJRCBzY2hlbWVJRD0iNiI+MjA2MDY4MjkyNjU8L2NiYzpJRD48L2NhYzpQYXJ0eUlkZW50aWZpY2F0aW9uPjxjYWM6UGFydHlOYW1lPjxjYmM6TmFtZT48IVtDREFUQVtFRkVDVElCSVRdXT48L2NiYzpOYW1lPjwvY2FjOlBhcnR5TmFtZT48Y2FjOlBhcnR5TGVnYWxFbnRpdHk+PGNiYzpSZWdpc3RyYXRpb25OYW1lPjwhW0NEQVRBW0VGRUNUSUJJVCBTQUNdXT48L2NiYzpSZWdpc3RyYXRpb25OYW1lPjxjYWM6UmVnaXN0cmF0aW9uQWRkcmVzcz48Y2JjOklEPjE1MDEzMjwvY2JjOklEPjxjYmM6QWRkcmVzc1R5cGVDb2RlPjAwMDA8L2NiYzpBZGRyZXNzVHlwZUNvZGU+PGNiYzpDaXR5U3ViZGl2aXNpb25OYW1lPkh1w6FzY2FyPC9jYmM6Q2l0eVN1YmRpdmlzaW9uTmFtZT48Y2JjOkNpdHlOYW1lPkxJTUE8L2NiYzpDaXR5TmFtZT48Y2JjOkNvdW50cnlTdWJlbnRpdHk+TElNQTwvY2JjOkNvdW50cnlTdWJlbnRpdHk+PGNiYzpEaXN0cmljdD5TYW4gSnVhbiBkZSBMdXJpZ2FuY2hvPC9jYmM6RGlzdHJpY3Q+PGNhYzpBZGRyZXNzTGluZT48Y2JjOkxpbmU+PCFbQ0RBVEFbTXouIDEyMyBMdC4gNyBHci4gMTUsIEh1w6FzY2FyLCBTYW4gSnVhbiBkZSBMdXJpZ2FuY2hvLCAxNTQxMl1dPjwvY2JjOkxpbmU+PC9jYWM6QWRkcmVzc0xpbmU+PGNhYzpDb3VudHJ5PjxjYmM6SWRlbnRpZmljYXRpb25Db2RlPlBFPC9jYmM6SWRlbnRpZmljYXRpb25Db2RlPjwvY2FjOkNvdW50cnk+PC9jYWM6UmVnaXN0cmF0aW9uQWRkcmVzcz48L2NhYzpQYXJ0eUxlZ2FsRW50aXR5PjwvY2FjOlBhcnR5PjwvY2FjOkFjY291bnRpbmdTdXBwbGllclBhcnR5PjxjYWM6QWNjb3VudGluZ0N1c3RvbWVyUGFydHk+PGNhYzpQYXJ0eT48Y2FjOlBhcnR5SWRlbnRpZmljYXRpb24+PGNiYzpJRCBzY2hlbWVJRD0iMSI+MDkzMzAwOTI8L2NiYzpJRD48L2NhYzpQYXJ0eUlkZW50aWZpY2F0aW9uPjxjYWM6UGFydHlMZWdhbEVudGl0eT48Y2JjOlJlZ2lzdHJhdGlvbk5hbWU+PCFbQ0RBVEFbRGVtbyBEZW1vbmlvXV0+PC9jYmM6UmVnaXN0cmF0aW9uTmFtZT48L2NhYzpQYXJ0eUxlZ2FsRW50aXR5PjwvY2FjOlBhcnR5PjwvY2FjOkFjY291bnRpbmdDdXN0b21lclBhcnR5PjxjYWM6UGF5bWVudFRlcm1zPjxjYmM6SUQ+Rm9ybWFQYWdvPC9jYmM6SUQ+PGNiYzpQYXltZW50TWVhbnNJRD5Db250YWRvPC9jYmM6UGF5bWVudE1lYW5zSUQ+PC9jYWM6UGF5bWVudFRlcm1zPjxjYWM6VGF4VG90YWw+PGNiYzpUYXhBbW91bnQgY3VycmVuY3lJRD0iVVNEIj4wLjAwPC9jYmM6VGF4QW1vdW50PjxjYWM6VGF4U3VidG90YWw+PGNiYzpUYXhhYmxlQW1vdW50IGN1cnJlbmN5SUQ9IlVTRCI+MS4yODwvY2JjOlRheGFibGVBbW91bnQ+PGNiYzpUYXhBbW91bnQgY3VycmVuY3lJRD0iVVNEIj4wPC9jYmM6VGF4QW1vdW50PjxjYWM6VGF4Q2F0ZWdvcnk+PGNhYzpUYXhTY2hlbWU+PGNiYzpJRD45OTk4PC9jYmM6SUQ+PGNiYzpOYW1lPklOQTwvY2JjOk5hbWU+PGNiYzpUYXhUeXBlQ29kZT5GUkU8L2NiYzpUYXhUeXBlQ29kZT48L2NhYzpUYXhTY2hlbWU+PC9jYWM6VGF4Q2F0ZWdvcnk+PC9jYWM6VGF4U3VidG90YWw+PC9jYWM6VGF4VG90YWw+PGNhYzpMZWdhbE1vbmV0YXJ5VG90YWw+PGNiYzpMaW5lRXh0ZW5zaW9uQW1vdW50IGN1cnJlbmN5SUQ9IlVTRCI+MS4yODwvY2JjOkxpbmVFeHRlbnNpb25BbW91bnQ+PGNiYzpUYXhJbmNsdXNpdmVBbW91bnQgY3VycmVuY3lJRD0iVVNEIj4xLjI4PC9jYmM6VGF4SW5jbHVzaXZlQW1vdW50PjxjYmM6UGF5YWJsZUFtb3VudCBjdXJyZW5jeUlEPSJVU0QiPjEuMjg8L2NiYzpQYXlhYmxlQW1vdW50PjwvY2FjOkxlZ2FsTW9uZXRhcnlUb3RhbD48Y2FjOkludm9pY2VMaW5lPjxjYmM6SUQ+MTwvY2JjOklEPjxjYmM6SW52b2ljZWRRdWFudGl0eSB1bml0Q29kZT0iWloiPjE8L2NiYzpJbnZvaWNlZFF1YW50aXR5PjxjYmM6TGluZUV4dGVuc2lvbkFtb3VudCBjdXJyZW5jeUlEPSJVU0QiPjEuMjg8L2NiYzpMaW5lRXh0ZW5zaW9uQW1vdW50PjxjYWM6UHJpY2luZ1JlZmVyZW5jZT48Y2FjOkFsdGVybmF0aXZlQ29uZGl0aW9uUHJpY2U+PGNiYzpQcmljZUFtb3VudCBjdXJyZW5jeUlEPSJVU0QiPjEuMjgxNTAxMzQwNTwvY2JjOlByaWNlQW1vdW50PjxjYmM6UHJpY2VUeXBlQ29kZT4wMTwvY2JjOlByaWNlVHlwZUNvZGU+PC9jYWM6QWx0ZXJuYXRpdmVDb25kaXRpb25QcmljZT48L2NhYzpQcmljaW5nUmVmZXJlbmNlPjxjYWM6VGF4VG90YWw+PGNiYzpUYXhBbW91bnQgY3VycmVuY3lJRD0iVVNEIj4wLjAwPC9jYmM6VGF4QW1vdW50PjxjYWM6VGF4U3VidG90YWw+PGNiYzpUYXhhYmxlQW1vdW50IGN1cnJlbmN5SUQ9IlVTRCI+MS4yODwvY2JjOlRheGFibGVBbW91bnQ+PGNiYzpUYXhBbW91bnQgY3VycmVuY3lJRD0iVVNEIj4wLjAwPC9jYmM6VGF4QW1vdW50PjxjYWM6VGF4Q2F0ZWdvcnk+PGNiYzpQZXJjZW50PjA8L2NiYzpQZXJjZW50PjxjYmM6VGF4RXhlbXB0aW9uUmVhc29uQ29kZT4zMDwvY2JjOlRheEV4ZW1wdGlvblJlYXNvbkNvZGU+PGNhYzpUYXhTY2hlbWU+PGNiYzpJRD45OTk4PC9jYmM6SUQ+PGNiYzpOYW1lPklOQTwvY2JjOk5hbWU+PGNiYzpUYXhUeXBlQ29kZT5GUkU8L2NiYzpUYXhUeXBlQ29kZT48L2NhYzpUYXhTY2hlbWU+PC9jYWM6VGF4Q2F0ZWdvcnk+PC9jYWM6VGF4U3VidG90YWw+PC9jYWM6VGF4VG90YWw+PGNhYzpJdGVtPjxjYmM6RGVzY3JpcHRpb24+PCFbQ0RBVEFbQ2FtYmlvIGRlIFVTRCBhIDQuNzggUEVOXV0+PC9jYmM6RGVzY3JpcHRpb24+PGNhYzpTZWxsZXJzSXRlbUlkZW50aWZpY2F0aW9uPjxjYmM6SUQ+ODQxMjE2MDM8L2NiYzpJRD48L2NhYzpTZWxsZXJzSXRlbUlkZW50aWZpY2F0aW9uPjwvY2FjOkl0ZW0+PGNhYzpQcmljZT48Y2JjOlByaWNlQW1vdW50IGN1cnJlbmN5SUQ9IlVTRCI+MS4yODE1MDEzNDA1PC9jYmM6UHJpY2VBbW91bnQ+PC9jYWM6UHJpY2U+PC9jYWM6SW52b2ljZUxpbmU+PC9JbnZvaWNlPlBLAQIUAAoAAAAAAPQcAlWkE1ZZtCIAALQiAAAbAAAAAAAAAAAAAAAAAAAAAAAyMDYwNjgyOTI2NS0wMy1CMDAwLTE4MS54bWxQSwUGAAAAAAEAAQBJAAAA7SIAAAAA';

 // xml post structure

$xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
				<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://service.sunat.gob.pe" xmlns:ns2="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
					<SOAP-ENV:Header>
						<ns2:Security>
							<ns2:UsernameToken>
								<ns2:Username>20606829265MODDATOS</ns2:Username>
								<ns2:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">moddatos</ns2:Password>
							</ns2:UsernameToken>
						</ns2:Security>
					</SOAP-ENV:Header>
					<SOAP-ENV:Body>
						<ns1:sendBill>
							<fileName>' . $data->filename . '.zip</fileName>
							<contentFile>' . $data->zipb64 . '</contentFile>
						</ns1:sendBill>
					</SOAP-ENV:Body>
				</SOAP-ENV:Envelope>';   // data from the form, e.g. some ID number

	$headers = array(
				"Content-type: text/xml;charset=\"utf-8\"",
				"Accept: text/xml",
				"Cache-Control: no-cache",
				"Pragma: no-cache",
				//~ "SOAPAction: http://connecting.website.com/WSDL_Service/GetPrice",
				"Content-length: ".strlen($xml_post_string),
			); //SOAPAction: your op URL

	$url = $soapUrl;

	// PHP cURL  for https connection with auth
	$ch = curl_init();
	//~ curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	//~ curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
	//~ curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	//~ curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	// converting
	$response = curl_exec($ch);
	curl_close($ch);

	echo $response;
