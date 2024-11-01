<table class="form-table" role="presentation">
    <tbody>
        <tr>
            <th scope="row">Render Sale Ribbon on Button?</th>
            <td>
                <input type="checkbox" id="simplerwc_should_render_sale_ribbon" name="simplerwc_should_render_sale_ribbon" value="1" {{ $should_render_sale_ribbon_checked }} />
                <label for="simplerwc_should_render_sale_ribbon">Render a sale ribbon on the button</label>
            </td>
        </tr>
        <tr>
            <th scope="row">Sale Ribbon Text</th>
            <td>
                <input id="simplerwc_sale_ribbon_text" name="simplerwc_sale_ribbon_text" type="text" maxlength="3" value="{{ $sale_ribbon_text }}" />
            </td>
        </tr>
    </tbody>
</table>